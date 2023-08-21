<?php

    namespace App\engine;

    class Response 
    {
        public static function outputJSON(array $data = []) :void {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
        
    }