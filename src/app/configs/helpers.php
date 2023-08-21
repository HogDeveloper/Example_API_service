<?php

    return [
        "migrate" => [
            "class" => App\engine\helpers\Migrate::class,
            "settings" => [
                "dependencies" => [
                    "orders" => App\migrations\tables\Orders::class
                ]
            ] 
        ],
        "seed" => [
            "class" => App\engine\helpers\Seed::class,
            "settings" => [
                "dependencies" => [
                    "orders" => App\migrations\seeds\Orders::class
                ], 
                "resources" => [
                    "csv" => "data.csv",
                    "xml" => "data.xml"
                ]
            ]
        ]
    ];