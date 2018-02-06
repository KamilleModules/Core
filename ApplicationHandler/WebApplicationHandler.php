<?php


namespace Module\Core\ApplicationHandler;


use Authenticate\SessionUser\SessionUser;
use Bat\LocalHostTool;
use Bat\ObTool;
use Chronos\Chronos;
use Core\Services\A;
use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Environment\Web\Environment;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Services\XConfig;
use Kamille\Architecture\Application\Web\WebApplication;
use Kamille\Architecture\Request\Web\FakeHttpRequest;
use Kamille\Architecture\Request\Web\HttpRequest;
use Kamille\Architecture\RequestListener\Web\ControllerExecuterRequestListener;
use Kamille\Architecture\RequestListener\Web\ResponseExecuterListener;
use Kamille\Architecture\RequestListener\Web\RouterRequestListener;
use Kamille\Services\XLog;
use Kamille\Utils\Routsy\LinkGenerator\ApplicationLinkGenerator;
use Kamille\Utils\Routsy\LinkGenerator\LinkGenerator;
use Kamille\Utils\Routsy\RoutsyRouter;
use Logger\Logger;
use Module\Core\Architecture\Router\AjaxStaticRouter;
use Module\Core\Architecture\Router\EarlyRouter;
use Module\Core\Architecture\Router\ExceptionRouter;
use Module\Core\Architecture\Router\MaintenanceRouter;
use Module\Core\Helper\CoreHelper;


class WebApplicationHandler
{


    public function handle(WebApplication $app)
    {
        Chronos::point("page.perf");
        try {

            $request = HttpRequest::create();

            //--------------------------------------------
            // INITIALIZE LOGGER
            //--------------------------------------------
            $logger = Logger::create();
            Hooks::call("Core_addLoggerListener", $logger);
            XLog::setLogger($logger); // now XLog is initialized for the rest of the application :)


            if (true === ApplicationParameters::get('debug')) {
                XLog::debug("[Core module] - WebApplicationHandler.handle with uri: " . $request->uri());
            }

            //--------------------------------------------
            // INITIALIZE PDO
            //--------------------------------------------
            if (true === XConfig::get("Core.useQuickPdo")) {
                A::quickPdoInit();
            }


            //--------------------------------------------
            // CONFIGURE SITE
            //--------------------------------------------
            // select front/back
            // decide if it's maintenance or not
            $this->configureSite($request);
            Hooks::call("Core_onSiteConfigured", $request);


//            $uri2Controller = [];
//            Hooks::call("Core_feedUri2Controller", $uri2Controller);


            $uri2Controllers = [];
            $ajaxRouter = AjaxStaticRouter::create();
            Hooks::call("Core_feedAjaxUri2Controllers", $uri2Controllers);
            $ajaxRouter->setUri2Controllers($uri2Controllers);


            $earlyRouter = EarlyRouter::create();
            $earlyRouter->addRouter(MaintenanceRouter::create()->setController(XConfig::get("Core.maintenanceController")));
            $earlyRouter->addRouter(ExceptionRouter::create()->setController(XConfig::get("Core.exceptionController")));
            $earlyRouter->addRouter($ajaxRouter);
            Hooks::call("Core_feedEarlyRouter", $earlyRouter);


            HtmlPageHelper::addMeta(['charset' => "UTF-8"]);


            $app
                ->addListener(RouterRequestListener::create()
                    ->addRouter($earlyRouter)
                    ->addRouter(X::get("Core_RoutsyRouter"))
//                    ->addRouter(StaticObjectRouter::create()
//                        ->setDefaultController(XConfig::get("Core.fallbackController"))
//                        ->setUri2Controller($uri2Controller))
//        ->addRouter(StaticPageRouter::create()
//            ->setStaticPageController(X::getStaticPageRouter_StaticPageController())
//            ->setUri2Page(X::getStaticPageRouter_Uri2Page()))
                )
                ->addListener(ControllerExecuterRequestListener::create()->setControllerRepresentationAdaptorCb(function ($controllerString) {

                    Hooks::call("Core_Controller_onControllerStringReceived", $controllerString);

                    $p = explode(':', $controllerString, 2);
                    if (2 === count($p)) {
                        // theme override
                        if (true === XConfig::get("Core.allowThemeControllerOverride", true)) {

                            $themeClass = 'Controller\Theme\\' . ucfirst(ApplicationParameters::get("theme"));
                            $themeClass .= substr($p[0], 10);
                            if (class_exists($themeClass)) {
                                $p[0] = $themeClass;
                                if (true === ApplicationParameters::get("debug")) {
                                    XLog::debug("[Core module] - WebApplicationHandler: controller overridden by theme: $themeClass");
                                }
                            }
                        }


                        $o = new $p[0];
                        return [$o, $p[1]];
                    }

                }))
                ->addListener(ResponseExecuterListener::create());


            $app->handleRequest($request);


        } catch (\Exception $e) {


            /**
             * @var $oldRequest HttpRequestInterface
             */
            $oldRequest = $app->get('request');
            if ($oldRequest instanceof HttpRequestInterface) {
                $sUri = $oldRequest->uri();
            } else {
                $sUri = " not set (no request key found in the HttpRequest object)";

            }
            XLog::error("[Core module] - WebApplicationHandler: exception caught with message: '" . $e->getMessage() . "'. uri was " . $sUri . ", redispatching to the fallback loop");

            if (true === XConfig::get("Core.showExceptionTrace")) {
                XLog::trace("$e");
            }

            ObTool::cleanAll(); // clean all buffers to avoid content leaks

            $request = FakeHttpRequest::create()
                ->set("oldRequest", $oldRequest)
                ->set("exception", $e);
            $app->handleRequest($request);
        }

        if (true) {
            // assuming xlog was successfully created
            $perf = Chronos::point("page.perf");
            $msg = PHP_SAPI . "-";
            if (array_key_exists("REQUEST_URI", $_SERVER)) {
                $msg .= $_SERVER['REQUEST_URI'];
            }
            $msg .= "--" . number_format($perf[0], 3) . "--" . $perf[1];
            XLog::log("$msg", "page.perf");
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function configureSite(HttpRequestInterface $request)
    {
        if (true === XConfig::get("Core.dualSite")) {
            if (true === CoreHelper::isBackoffice($request)) {
                $request->set("siteType", "dual.back");
                SessionUser::$key = 'backUser';
                ApplicationParameters::set("theme", XConfig::get("Core.themeBack"));
                ApplicationRegistry::set("isBackoffice", true);

            } else {
                $request->set("siteType", "dual.front");
                SessionUser::$key = 'frontUser';
                ApplicationParameters::set("theme", XConfig::get("Core.themeFront"));
                ApplicationRegistry::set("isBackoffice", false);
            }
        } else {
            $request->set("siteType", "single");
        }

    }
}
