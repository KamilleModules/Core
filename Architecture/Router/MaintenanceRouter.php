<?php


namespace Module\Core\Architecture\Router;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\Web\WebRouterInterface;
use Kamille\Services\XConfig;
use Module\Application\Api\Layer\ApplicationVariablesLayer;

class MaintenanceRouter implements WebRouterInterface
{

    /**
     * The controller to handle the exception (probably a string)
     */
    private $controller;

    public static function create()
    {
        return new static();
    }

    public function match(HttpRequestInterface $request)
    {
        $isInMaintenance = (bool)ApplicationVariablesLayer::getVariable("Core_isInMaintenance", XConfig::get("Core.isInMaintenance"));
        if (true === $isInMaintenance &&
            (
                "single" === $request->get("siteType") ||
                "dual.front" === $request->get("siteType")
            )
        ) {
            return $this->controller;
        }
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

}