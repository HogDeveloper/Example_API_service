<?php

    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

    require_once __DIR__  . "/vendor/autoload.php";
    require_once __DIR__ . "/app/configs/bootstrap.php";

    use App\engine\Api;

    $api = new Api(ROUTES_CONFIG);
    $api->apply();

    exit();
    
?>