<?php
class DB {
	private static $instance;
	
	function __construct() {
		if (!class_exists("mysqli"))
			throw new Exception("Cannot instantiate database provider mysqli");
		
		$this->mysqli = new mysqli(null, DB_USER, DB_PASSWORD, DB_NAME, null, 
			(file_exists("/var/lib/mysql/mysql.sock") ? "/var/lib/mysql/mysql.sock" : "/var/run/mysqld/mysqld.sock"));

		mysqli_set_charset($this->mysqli, 'utf8');
		if ($this->mysqli->connect_errno) {
			$msg = "Failed to connect to MySQL: " . $mysqli->connect_error;
			throw new Exception($msg);
		}
	}

	function __destruct() {
		$this->mysqli->close();
	}
	
	public static function getInstance() {
		if (self::$instance == null)
			self::$instance = new DB();
		return self::$instance;
	}

	public function put($queryString, $dataType = "", ...$params) {
		$stmt = $this->mysqli->prepare($queryString);
		if (!$stmt) {
			$msg = "Failed to prepare SQL query $queryString: " . $this->mysqli->error;
			throw new Exception($msg);
		}
		if (count($params) > 0) $stmt->bind_param($dataType, ...$params);
		if (!$stmt->execute()) {
			$msg = "Failed to execute SQL query $queryString: " . $this->mysqli->error;
			throw new Exception($msg);
		}
		$stmt->close();
	}

	private static function stmt_bind_assoc(&$stmt, &$bound_assoc) {
		$meta = $stmt->result_metadata();
		$fields = array();
		$bound_assoc = array();
		$fields[] = $stmt;
		while ($field = $meta->fetch_field())
			$fields[] = &$bound_assoc[$field->name];
		call_user_func_array("mysqli_stmt_bind_result", $fields);
	}

	private function arrayCopy($a) {
		return array_map(function($e) {
			return ((is_array($e))
				? call_user_func(__FUNCTION__, $e)
				: ((is_object($e))
					? clone $e
					: $e ) ); }, $a);
	}

	public function get($queryString, $dataType = "", ...$params) {
		$stmt = $this->mysqli->prepare($queryString);
		if (!$stmt) {
			$msg = "Failed to prepare SQL query $queryString: " . $this->mysqli->error;
			throw new Exception($msg);
		}
		if (count($params) > 0) $stmt->bind_param($dataType, ...$params);
		if (!$stmt->execute()) {
			$msg = "Failed to execute SQL query $queryString: " . $this->mysqli->error;
			throw new Exception($msg);
		}
		$rows = [];
		try {
			self::stmt_bind_assoc($stmt, $row);
			while ($stmt->fetch())
				array_push($rows, self::arrayCopy($row));
		} catch (Exception $e) {
			$msg = "Failed to retrieve results for SQL query $queryString: " . $this->mysqli->error;
			throw new Exception($msg);
		}
		$stmt->close();
		return $rows;
	}

	public function call($queryString) {
		$result = $this->mysqli->query($queryString);
		if (!$result) {
			$msg = "Failed to execute SQL call $queryString: " . $this->mysqli->error;
			throw new Exception($msg);
		}

		$rows = [];
		while ($row = mysqli_fetch_assoc($result))
			array_push($rows, $row);

		mysqli_free_result($result);
		while (mysqli_more_results($this->mysqli)) { mysqli_next_result($this->mysqli); }
		return $rows;
	}
}
?>
