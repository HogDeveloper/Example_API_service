<?php

    namespace App\engine;

    use Exception;

    class Cli 
    {
        private $args = [];
        private $helpers = [];
        private $rootDir = "";
        public $responseText = "";
        public function __construct(array $args, string $pathToConstants, string $rootDir) {
            $this->rootDir = $rootDir;

            if(!file_exists($pathToConstants)) {
                throw new Exception("Error: File bootstrap not found");
            }

            require_once $pathToConstants;

            if(!file_exists($rootDir . CONFIG_DIR . "helpers.php")) {
                throw new Exception("Error: File config helpers not found");
            }

            $this->helpers = require_once $rootDir . CONFIG_DIR . "helpers.php";

            unset($args[0]);
            $this->args = $args;
        }

        public function exec() :void {
            $handlers = $this->formatingArgs();
            $className = $handlers["className"];
            $methodName = $handlers["methodName"];

            if(array_key_exists($className, $this->helpers)) {
                try {
                    $class = new $this->helpers[$className]["class"]($this, $this->helpers[$className]["settings"]);
                    $class->action($this->args, $methodName);
                } catch (\Throwable $e) {
                    $this->responseText = $e->getMessage();
                }
                
                if($this->responseText !== "") {
                    echo $this->responseText."\r\n";
                }
            }
        }

        public function getRootDir() :string {
            return $this->rootDir; 
        }

        private function formatingArgs() :array {
            $result = [];
            $tmpPart = explode(":", array_shift($this->args));
            $result["className"] = array_shift($tmpPart);
            $result["methodName"] = "";

            $tmpArr = explode("-", array_shift($tmpPart));

            foreach($tmpArr as $part) {
                if(empty($result["methodName"])) {
                    $result["methodName"] .= $part;
                    continue;
                }
                $result["methodName"] .= ucfirst($part);
            }

            return $result;
        }
        
    }