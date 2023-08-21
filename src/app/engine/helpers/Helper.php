<?php

    namespace App\engine\helpers;

    use Throwable;

    abstract class Helper
    {
        protected $cli = null;
        protected $appDir = "";
        protected $settings = [];
        protected $dbConfig = "";

        protected function getClassName(array $array) :array {
            $result = [];
            $result["className"] = array_shift($array);
            $result["params"] = [];

            foreach($array as $item) {
                $result["params"][] = $item;
            }

            return $result;
        }

        protected function getHandler(array $params, string $methodName) {
            $expParams = $this->getClassName($params);
            
            if(array_key_exists($expParams["className"], $this->settings["dependencies"])) {
                $class = null;

                try{
                    $class = new $this->settings["dependencies"][$expParams["className"]]($this->cli, $expParams["params"], $this->settings, $this->dbConfig);
                    $class->$methodName($params);
                } catch (Throwable $e) {
                    $this->cli->responseText = $e->getMessage();
                }

                return $class;
            }
        }
    }