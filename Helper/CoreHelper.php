<?php


namespace Module\Core\Helper;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Services\XConfig;

class CoreHelper
{

    public static function isBackoffice(HttpRequestInterface $request)
    {
        $backUri = XConfig::get("Core.uriNamespaceBackoffice");
        $uri = $request->uri(false);
        return (
            $backUri === $uri ||
            0 === strpos($uri, $backUri . "/")
        );
    }
}