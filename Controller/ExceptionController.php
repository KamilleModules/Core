<?php


namespace Controller\Core;


use Core\Controller\ApplicationController;
use Kamille\Architecture\Application\Web\WebApplication;
use Kamille\Utils\Laws\Config\LawsConfig;

class ExceptionController extends ApplicationController
{


    public function render()
    {
        $request = WebApplication::inst()->get("request");
        $e = $request->get("exception");


        // using lnc1.splash
        return $this->renderByViewId("Core/exception", LawsConfig::create()->replace([
            "widgets" => [
                "main.exception" => [
                    "conf" => [
                        "exception" => $e,
                    ],
                ],
            ],
        ]));
    }


}