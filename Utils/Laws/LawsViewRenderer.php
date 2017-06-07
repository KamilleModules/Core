<?php


namespace Module\Core\Utils\Laws;


use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Kamille\Utils\Laws\Config\LawsConfig;
use Kamille\Utils\Laws\LawsUtil;
use Kamille\Utils\Laws\LawsUtilInterface;
use Kamille\Utils\Laws\ThemeCollection\ThemeCollection;
use Module\Core\Mvc\WidgetInstanceDecorator\WidgetInstanceDecorator;


class LawsViewRenderer
{



    /**
     * @param $viewId
     * @param LawsConfig|null $config
     * @param array $options
     * @return HttpResponseInterface
     */
    public function renderByViewId($viewId, LawsConfig $config = null, array $options = [])
    {


        //--------------------------------------------
        // SEND DEBUG MESSAGE TO THE LOGS
        //--------------------------------------------
        if (true === ApplicationParameters::get('debug')) {
            XLog::debug("[Core module] - LawsViewRenderer.renderByViewId with viewId $viewId");
        }


        //--------------------------------------------
        // CONFIGURING THE LAWS UTIL OPTIONS
        //--------------------------------------------
        if (false === array_key_exists("autoloadCss", $options)) {
            $options['autoloadCss'] = XConfig::get("Core.useCssAutoload", false);
        }
        $options['widgetClass'] = 'Core\Mvc\Widget\ApplicationWidget';



        //--------------------------------------------
        // WIDGET INSTANCE DECORATION
        // see widget-instance-decorator.md document for more info
        //--------------------------------------------
        $options['widgetInstanceDecorator'] = WidgetInstanceDecorator::create();



        //--------------------------------------------
        // LET MODULES UPDATE THE LAWS CONFIG
        //--------------------------------------------
        if (null === $config) {
            $config = LawsConfig::create();
        }
        $c = [$this, $config];
        Hooks::call("Core_autoLawsConfig", $c);


        /**
         * Laws3: https://github.com/lingtalfi/laws
         */
        if (false !== ($theme = ThemeCollection::getTheme(ApplicationParameters::get("theme")))) {
            $theme->configureView($viewId, $config);
        }


        //--------------------------------------------
        // INJECTING LAZY JS CODE AT THE END OF THE BODY
        //--------------------------------------------
        if (null !== ($coll = X::get("Core_lazyJsInit", null, false))) {
            $options['bodyEndSnippetsCollector'] = $coll;
        }


        //--------------------------------------------
        // RENDER THE CONTENT USING THE LAWS TOOL
        //--------------------------------------------
        /**
         * @var $util LawsUtilInterface
         */
        $util = X::get("Core_lawsUtil");
        $content = $util->renderLawsViewById($viewId, $config, $options);
        return HttpResponse::create($content);
    }


}