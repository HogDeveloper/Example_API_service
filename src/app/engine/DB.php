<?php 

	namespace App\engine;

	use mysqli;
	use Throwable;

	class DB 
	{
		private $connectParams = [];
		private $port = "3306";
		private $connection;
		protected $queryString;
		public $aliases = [];
		public $replacesCharacter = ["-", " "];
		public $defaultLimit = 2;
		public $defaultOffset = 1;
		public $exceptionFiltes = ["limit", "offset"]; // etc

		public function __construct(string $pathToConfig = "") {
			$this->connectParams = ($pathToConfig === "") ? require_once DB_CONFIG : require_once $pathToConfig;

			$this->connection = new mysqli(
				$this->connectParams["hostname"],
				$this->connectParams["username"], 
				$this->connectParams["password"], 
				$this->connectParams["database"], 
				$this->port
			);

			if($this->connection->connect_error) {
				throw new \Exception('Error: ' . $this->connection->connect_error . '<br />Error No: ' . $this->connection->connect_errno);
			}

			$this->connection->set_charset("utf8");
			$this->connection->query("SET SQL_MODE = ''");
			$this->connection->query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
		}

		public function getDBName() :string {
			return $this->connectParams["database"];
		}

		protected function query(string $sqlQuery) :DB {
			$this->queryString = $sqlQuery;
			return $this;
		}

		protected function where(array $conditions) :DB {
			if(empty($conditions)) {
				return $this;
			}
		
			$subString = " WHERE ";

			foreach($conditions as $name => $value) {
				if(in_array($name, $this->exceptionFiltes)) {
					continue;
				}
				$subString .= ""."`".$name."`='".$value."'"." AND";
			}

			if($subString === " WHERE ") {
				return $this;
			}
			$this->queryString .= trim($subString, " AND");
			return $this;
		}

		protected function limit(int $limit) :DB {
			if($limit === 0) {
				return $this;
			}

			$this->queryString .= " LIMIT " . $limit;
			return $this;
		}

		protected function offset(int $offset) :DB {
			if($offset === 0) {
				return $this;
			}
			
			$offset = ($offset === 1) ? ($offset - 1) : $offset;
			$this->queryString .= " OFFSET " . $offset;
			return $this;
		}

		protected function setValues(array $values = []) :DB {
			if(empty($values)) {
				return $this;
			}
			$values = array_values($values);
			$subString = "";

			foreach ($values as $value) {
				$lastEl = count($value) - 1;
				$item = array_values($value);
				$part = "(";
				for($i = 0; $i <= $lastEl; $i++) {
					if($i !== $lastEl) {
						$part .= "'".trim($item[$i])."', ";
						continue;
					}
					$part .= "'".trim($item[$i])."'";
				}
				$part .= "),";
				$subString .= $part;
			}
			$subString = trim($subString, ",");
			$this->queryString .= $subString;
			return $this;
		}

		protected function setColumsName(array $headers) :DB {
			$headers = $this->formatingColumnsName($headers);
			$lastEl = count($headers) - 1;
			$subString = "(";

			for($i = 0; $i <= $lastEl; $i++) {
				if($i !== $lastEl) {
					$subString .= $headers[$i].", ";
					continue;
				}
				$subString .= $headers[$i];
			}
			$subString .= ") VALUES";
			$this->queryString .= $subString;
			return $this;
		}

		private function formatingColumnsName(array $headers) :array {
			if(empty($this->aliases)) {
				return $headers;
			}
			
			foreach($headers as $key => $header) {	
				$header = trim($header);
				if(array_key_exists($header, $this->aliases)) {
					$headers[$key] = "`".$this->aliases[$header]."`";
					continue;
				}
				$headers[$key] = "`".str_replace($this->replacesCharacter, "_", $header)."`";
			}

			return $headers;
		}

		protected function updateDuplicate(array $columns = []) :DB {
			if(empty($columns)) {
				return $this;
			}
			$subString = " ON DUPLICATE KEY UPDATE ";

			foreach($columns as $column){
				$subString .= "`".$column."`=VALUES(`".$column."`),";
			}

			$subString = trim($subString, ",");
			$this->queryString .= $subString . " ";
			return $this;
		}

		protected function exec() :array|bool {

			try {
				$query = $this->connection->query($this->queryString);
			} catch (Throwable $e) {
				return [$e->getMessage()];
			}

			if(!$this->connection->errno) {
				if($query instanceof \mysqli_result) {
					$data = array();

					while($row = $query->fetch_assoc()) {
						$data[] = $row;
					}

					$result = new \stdClass();
					$result->num_rows = $query->num_rows;
					$result->row = isset($data[0]) ? $data[0] : array();
					$result->rows = $data;
					$query->close();
					$this->queryString = '';

					return $result->rows;
				} else {
					return true;
				}
			} else {
				throw new \Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $this->queryString);
			}
		}

		public function __destruct() {
			$this->connection->close();
		}
	}