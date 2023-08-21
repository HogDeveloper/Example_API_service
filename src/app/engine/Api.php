<?php 

    namespace App\engine;

    class Api 
    {
        private $requestUri;
        private $routes;
        private $method;

        public function __construct(string $pathToRoutesConfig) {

            if (file_exists($pathToRoutesConfig)){
                $this->routes = require_once $pathToRoutesConfig;
            } else {
                throw new \ErrorException("Error: routes config not found.");
            }

            $this->requestUri = trim($_SERVER['REQUEST_URI'],'/');
            $this->method = $_SERVER['REQUEST_METHOD'];

            if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                    $this->method = 'DELETE';
                } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                    $this->method = 'PUT';
                } else {
                    throw new \Exception("Unexpected Header");
                }
            }
        }

        public function apply() :void {
            if(method_exists($this, "handler".$this->method)) {
                $this->{"handler$this->method"}();
            }
        }

        private function handlerGET() :void {
            foreach($this->routes as $pattern => $route) {
                if(preg_match("#^".$pattern."$#", $this->requestUri)){ 
                    $params = preg_replace("#^".$pattern."$#", $route[$this->method]["params"], urldecode($this->requestUri));
                    $params = trim(mb_substr($params, strpos($params, "?")), "?");
                    $params = $this->formatingParams(explode("&", $params));
                    $controller = new $route[$this->method]["controller"]();
                    call_user_func_array([$controller, $route[$this->method]["action"]], ["params" => $params]);
                    break;                    
                }
            }
        }

        private function formatingParams(array $params) :array {
            $result = [];

            foreach ($params as $param){                
                if(empty($param)) {
                    break;
                }
                
                $tmp = explode("=", $param);
                $key = $tmp[0];
                $value = $tmp[1];
                $result[$key] = $value;
            }
            return $result;
        }

    }