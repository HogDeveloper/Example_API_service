<?php

    define('APP_DIR', $_SERVER["DOCUMENT_ROOT"]);
    define('CONFIG_DIR', APP_DIR . "/app/configs/");
    define('RESOURCES_DIR', APP_DIR . "/resources/");

    define("ROUTES_CONFIG", CONFIG_DIR . "routes.php");
    define("DB_CONFIG", CONFIG_DIR . "db.php");
    define("BOOTSTRAP_CONFIG", CONFIG_DIR . "bootsrap.php");