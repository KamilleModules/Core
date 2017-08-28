<?php


namespace Module\Core;


class CoreHooks
{

//    protected static function Core_feedUri2Controller(array &$uri2Controller)
//    {
//
//    }

    protected static function Core_configureLawsUtil(\Kamille\Utils\Laws\LawsUtil $util)
    {

    }

    protected static function Core_addLoggerListener(\Logger\LoggerInterface $logger)
    {
        if (true === \Kamille\Services\XConfig::get("Core.useFileLoggerListener")) {
            $f = \Kamille\Services\XConfig::get("Core.logFile");
            $logger->addListener(\Logger\Listener\FileLoggerListener::create()
                ->setFormatter(\Logger\Formatter\TagFormatter::create())
                ->setIdentifiers(null)
                ->removeIdentifier("sql.log")
                ->removeIdentifier("tabatha")
                ->setPath($f));
        }


        if (true === \Kamille\Services\XConfig::get("Core.useDbLoggerListener")) {

            $f = \Kamille\Services\XConfig::get("Core.dbLogFile");
            $logger->addListener(\Logger\Listener\FileLoggerListener::create()
                ->setFormatter(\Logger\Formatter\TagFormatter::create())
                ->setIdentifiers(null)
                ->setPath($f));
        }
    }

    protected static function Core_feedEarlyRouter(\Module\Core\Architecture\Router\EarlyRouter $router)
    {

    }


    protected static function Core_feedOnTheFlyFormProvider(\OnTheFlyForm\Provider\OnTheFlyFormProviderInterface $provider)
    {

    }

    protected static function Core_onSiteConfigured(\Kamille\Architecture\Request\Web\HttpRequestInterface $request)
    {

    }

    /**
     * @param data , array:
     *      - 0: controller instance
     *      - 1: laws config
     */
    protected static function Core_autoLawsConfig(&$data)
    {

    }

    protected static function Core_feedAjaxUri2Controllers(array &$uri2Controllers)
    {

    }


    protected static function Core_addLawsUtilProxyDecorators(\Kamille\Mvc\LayoutProxy\LawsLayoutProxyInterface $layoutProxy)
    {
        if ($layoutProxy instanceof \Kamille\Mvc\LayoutProxy\LawsLayoutProxy) {
            $layoutProxy->addDecorator(\Kamille\Mvc\WidgetDecorator\PositionWidgetDecorator::create());
        }
    }

    protected static function Core_lazyJsInit_addCodeWrapper(\Module\Core\JsLazyCodeCollector\JsLazyCodeCollectorInterface $collector)
    {
        if (true === \Kamille\Services\XConfig::get('Core.addJqueryEndWrapper')) {
            $collector->addCodeWrapper('jquery', function ($s) {
                $r = '$(document).ready(function () {' . PHP_EOL;
                $r .= $s;
                $r .= '});' . PHP_EOL;
                return $r;
            });
        }
    }

    protected static function Core_ModalGscpResponseDefaultButtons(array &$buttons)
    {

    }

    protected static function Core_configureRoutsyRouter(\Kamille\Utils\Routsy\RoutsyRouter $router)
    {

    }


    protected static function Core_widgetInstanceDecorator(\Kamille\Mvc\Widget\WidgetInterface $widget)
    {
        $widget->setTemplate("Core/widget-error");
    }

    protected static function Core_feedListModifierCircle(\ListModifier\Circle\ListModifierCircle $circle)
    {
    }
}


