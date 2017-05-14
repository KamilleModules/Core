<?php


namespace Module\Core\Architecture\Router;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\Web\WebRouterInterface;

class EarlyRouter implements WebRouterInterface
{

    /**
     * @var WebRouterInterface[]
     */
    private $routers;

    public function __construct()
    {
        $this->routers = [];
    }


    public static function create()
    {
        return new static();
    }

    public function addRouter(WebRouterInterface $router)
    {
        $this->routers[] = $router;
        return $this;
    }

    public function match(HttpRequestInterface $request)
    {
        foreach ($this->routers as $router) {
            if (null !== ($res = $router->match($request))) {
                return $res;
            }
        }
    }
}