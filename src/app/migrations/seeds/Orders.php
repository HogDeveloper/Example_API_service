<?php

    namespace App\migrations\seeds;

    use App\engine\Cli;
    use App\engine\DB;
    use League\Csv\Reader;
    use Throwable;

    class Orders extends DB
    {
        private $cli = null;
        private $params = [];
        private $settings = [];
        private $tableName = "orders";
        public $aliases = [
            "ship-to name" => "customer_name",
            "grant total (purchased)" => "grant_total",
        ];
        private $allowedResources = ["csv", "xml"];

        public function __construct(Cli $cli, array $params, array $settings, string $dbConfig) {
            $this->params = $params;
            $this->cli= $cli;
            $this->settings = $settings;

            parent::__construct($dbConfig);
        }

        public function fillTable() {
            if(in_array($this->params[0], $this->allowedResources)) {
                if($this->params[0] === "csv") {
                    $this->csvParser();
                } elseif($this->params[0] === "xml") {
                    $this->xmlParser();
                }
            }
        }

        private function csvParser() {
            if(file_exists($this->cli->getRootDir() . RESOURCES_DIR . $this->settings["resources"]["csv"])) {
                $csv = Reader::createFromPath($this->cli->getRootDir() . RESOURCES_DIR . $this->settings["resources"]["csv"], 'r');
                $csv->setHeaderOffset(0);
                $headers = $csv->getHeader();
                $records = $csv->getRecords();
                $values = [];
                foreach ($records as $record) {
                    $values[] = $record;
                }

                try {
                    $this->query("INSERT INTO `".$this->tableName."` ")
                    ->setColumsName($headers)
                    ->setValues($values)
                    ->updateDuplicate(["customer_email", "grant_total"])
                    ->exec();

                    $this->cli->responseText = "Table update was successful";
                } catch (Throwable $e) {
                    $this->cli->responseText = $e->getMessage();
                }
            }
        }

        private function xmlParser() {
            if(file_exists($this->cli->getRootDir() . RESOURCES_DIR . $this->settings["resources"]["xml"])) {
                $xml = simplexml_load_file($this->cli->getRootDir() . RESOURCES_DIR . $this->settings["resources"]["xml"]);
                $rows = [];

                foreach($xml as $key => $value) {
                    if(empty($value)) {
                        continue;
                    }
                    $rows = $value->Table->Row;
                }
                $values = $this->processCells($rows);
                $headers = array_shift($values);

                try {
                    $this->query("INSERT INTO `".$this->tableName."` ")
                    ->setColumsName($headers)
                    ->setValues($values)
                    ->updateDuplicate(["customer_email", "grant_total"])
                    ->exec();

                    $this->cli->responseText = "Table update was successful";
                } catch (Throwable $e) {
                    $this->cli->responseText = $e->getMessage();
                }
            }
        }

        private function processCells($element) {
            $result = [];
        
            foreach ($element as $key => $value) {
                if ($key === 'Cell') {
                    $cellData = $this->processCellData($value);
                    foreach($cellData as $cell) {
                        $result[] = $cell;
                    }
                } 
                elseif ($key === 'Row') {
                    $result[] = $this->processCells($value);
                }
            }
        
            return $result;
        }
        
        private function processCellData($cell) {
            $cellData = [];
        
            foreach ($cell as $key => $value) {
                if ($key === 'Data') {
                    $cellData[] = (string) $value;
                }
            }
            return $cellData;
        }

    }