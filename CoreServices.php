<?php


namespace Module\Core;


class CoreServices
{

    protected static function Core_webApplicationHandler()
    {
        return new \Module\Core\ApplicationHandler\WebApplicationHandler();
    }

    /**
     * This service will always return an instance of the LawsUtil object.
     * Happy coding!
     */
    protected static function Core_lawsUtil()
    {
        $layoutProxy = \Kamille\Mvc\LayoutProxy\LawsLayoutProxy::create();
        \Core\Services\Hooks::call("Core_addLawsUtilProxyDecorators", $layoutProxy);
        $util = \Kamille\Utils\Laws\LawsUtil::create()
            ->setLawsLayoutProxy($layoutProxy);
        \Core\Services\Hooks::call("Core_configureLawsUtil", $util);
        return $util;
    }


    protected static function Core_lazyJsInit()
    {
        $collector = \Module\Core\JsLazyCodeCollector\JsLazyCodeCollector::create();
        \Core\Services\Hooks::call("Core_lazyJsInit_addCodeWrapper", $collector);
        return $collector;
    }

    protected static function Core_QuickPdoInitializer()
    {
        $initializer = new \Module\Core\Pdo\QuickPdoInitializer();
        return $initializer;
    }

    protected static function Core_PersistentRowCollectionFinder()
    {
        $initializer = new \Core\Framework\PersistentRowCollection\Finder\PersistentRowCollectionFinder();
        return $initializer;
    }

    protected static function Core_LawsViewRenderer()
    {
        $r = new \Module\Core\Utils\Laws\LawsViewRenderer();
        return $r;
    }

    protected static function Core_RoutsyRouter()
    {
        $routsyRouter = \Kamille\Utils\Routsy\RoutsyRouter::create();
        $routsyRouter
            ->addCollection(\Kamille\Utils\Routsy\RouteCollection\RoutsyRouteCollection::create()->setFileName("routes"))
            ->addCollection(\Kamille\Utils\Routsy\RouteCollection\PrefixedRoutsyRouteCollection::create()
                ->setFileName("back")
                ->setOnRouteMatch(function () {
                    \Kamille\Architecture\ApplicationParameters\ApplicationParameters::set("theme", \Kamille\Services\XConfig::get("Core.themeBack"));
                })
                ->setUrlPrefix(\Kamille\Services\XConfig::get("Core.uriPrefixBackoffice"))
            );
        \Core\Services\Hooks::call("Core_configureRoutsyRouter", $routsyRouter);
        return $routsyRouter;
    }

    protected static function Core_TabathaCache()
    {
        if (true === \Kamille\Architecture\ApplicationParameters\ApplicationParameters::get("debug")) {
            $r = new \Module\Core\Planets\TabathaCache\DebugTabathaCache();
        } else {
            $r = new \TabathaCache\Cache\TabathaCache();
        }

        $r->setDefaultForceGenerate((false === \Kamille\Services\XConfig::get("Core.enableTabathaCache")));

        $r->setDir(\Kamille\Architecture\ApplicationParameters\ApplicationParameters::get("app_dir") . "/cache/tabatha");
        return $r;
    }
}


