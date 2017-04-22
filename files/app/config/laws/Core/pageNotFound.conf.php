<?php


$conf = [
    "layout" => [
        "name" => "splash/default",
    ],
    "widgets" => [
        "main.httpError" => [
            "name" => "HttpError/default",
            "tpl" => [
                "code" => 404,
                "text" => "Page not found",
            ],
        ],
    ],
];