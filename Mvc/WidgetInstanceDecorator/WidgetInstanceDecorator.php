<?php


namespace Module\Core\Mvc\WidgetInstanceDecorator;


use Core\Services\Hooks;
use Kamille\Mvc\Widget\WidgetInterface;
use Kamille\Mvc\WidgetInstanceDecorator\WidgetInstanceDecoratorInterface;

class WidgetInstanceDecorator implements WidgetInstanceDecoratorInterface
{


    public static function create()
    {
        return new static();
    }

    /**
     * Implementation of kamille's widget-instance-decorator idea, see the widget-instance-decorator.md document
     * in kamille's repository.
     */
    public function decorate(WidgetInterface $widget, array $conf)
    {
        if (
            array_key_exists('errorCode', $conf) &&
            array_key_exists('errorMessage', $conf)
        ) {
            Hooks::call("Core_widgetInstanceDecorator", $widget);
        }
    }

}