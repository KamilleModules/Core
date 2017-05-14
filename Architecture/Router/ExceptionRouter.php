<?php


namespace Module\Core\Architecture\Router;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\Web\WebRouterInterface;

class ExceptionRouter implements WebRouterInterface
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
        if (null !== ($exception = $request->get('exception'))) {
            return $this->controller;
        }
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

}