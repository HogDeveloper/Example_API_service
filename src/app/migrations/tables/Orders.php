<?php

    namespace App\migrations\tables;

    use App\engine\Cli;
    use App\engine\DB;
    use Throwable;

    class Orders extends DB
    {
        private $cli = null;
        private $tableName = "orders";
        private $settings = [];
        private $params = []; // for other params

        public function __construct(Cli $cli, array $params, array $settings, string $dbConfig) {
            $this->params = $params;
            $this->cli= $cli;
            $this->settings = $settings;

            parent::__construct($dbConfig);
        }

        public function createTable() :void {
            if(empty($this->query("SHOW TABLES FROM `".$this->getDBName()."` like '".$this->tableName."';")->exec())) {
                try {
                    $queryCreate = "CREATE TABLE IF NOT EXISTS `".$this->tableName."` (
                        `id` varchar(255) NOT NULL,
                        `purchase_date` varchar(255) NOT NULL,
                        `customer_name` varchar(96) NOT NULL,
                        `customer_email` varchar(96) NOT NULL,
                        `grant_total` double(10,2) NOT NULL,
                        `status` varchar(32) NOT NULL
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;";

                    $queryAlter = "ALTER TABLE `".$this->tableName."`
                        ADD PRIMARY KEY (`id`),
                        ADD KEY `id` (`id`,`customer_name`),
                        ADD KEY `id_2` (`id`,`customer_email`)";


                    $this->query($queryCreate)->exec();
                    $this->query($queryAlter)->exec();
            
                    $this->cli->responseText = "The table `".$this->tableName."` was created successfully";
                } catch (Throwable $e) {
                    $this->cli->responseText = $e->getMessage();
                }
            }
        }

    }