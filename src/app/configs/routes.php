<?php

    return [
        "order/find/?(.+)=(.+)" => [
            "GET" => [
                "controller" => "App\controllers\Orders",
                "action" => "find",
                "params" => "$1=$2"
            ]
        ],
        "order/find" => [
            "GET" => [
                "controller" => "App\controllers\Orders",
                "action" => "find",
                "params" => ""
            ]
        ]
    ];