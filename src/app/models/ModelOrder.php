<?php

    namespace App\models;

    use App\engine\DB;

    class ModelOrder extends DB
    {
        private $tableName = "orders";

        public function getOrders(array $params = []) {
            if(empty($params)) {
                return $this->query("SELECT * FROM `".$this->tableName."` ")->exec();
            }

            $this->query("SELECT * FROM `".$this->tableName."` ")
                ->where($params);

            if(isset($params["limit"])) {
                $this->limit($params["limit"]);

                if(isset($params["offset"])) {
                    $this->offset($params["offset"]);
                }
            }

            $result = $this->exec();

            if(isset($params["offset"]) && isset($params["limit"])) {
                $result[] = ["currentPage" => $params["offset"]];
            }

            return $result;
        }

    }