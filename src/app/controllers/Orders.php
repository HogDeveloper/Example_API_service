<?php

    namespace App\controllers;

    use App\engine\Response;
    use App\models\ModelOrder;

    class Orders 
    {
        public function find(array $params = []) :void {
            $modelOrder = new ModelOrder();
            $response = $modelOrder->getOrders($params);
            
            Response::outputJSON($response);
        }

    }