<?php


namespace Module\Core\Architecture\Router;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\Helper\RouterHelper;
use Kamille\Architecture\Router\Web\WebRouterInterface;

class AjaxStaticRouter implements WebRouterInterface
{

    private $uri2Controllers;


    public function __construct()
    {
        $this->uri2Controllers = [];
    }

    public static function create()
    {
        return new static();
    }

    public function match(HttpRequestInterface $request)
    {
        if ("XMLHttpRequest" === $request->header('X-Requested-With')) {
            $uri = $request->uri(false);
            if (array_key_exists($uri, $this->uri2Controllers)) {
                $controller = $this->uri2Controllers[$uri];
                return $controller;
            }
        }
    }

    public function setUri2Controllers(array $uri2Controllers)
    {
        $this->uri2Controllers = $uri2Controllers;
        return $this;
    }

}