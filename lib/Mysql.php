<?php

namespace lib;


class Mysql {

	protected $mysql;

	public function __construct() {
		include(__DIR__ . '/../conf/config.php');
		$this->mysql = mysqli_connect(
			$config['mysql']['host'],
			$config['mysql']['user'],
			$config['mysql']['password'],
			$config['mysql']['database']
		);
	}

	public function query($sql) {
		return $this->mysql->query($sql);
	}

	public function startTransaction() {
		$this->mysql->begin_transaction();
	}
	public function completeTransaction() {
		$this->mysql->commit();
	}
	public function select($sql) {
		$result = $this->query($sql);
		$rows = array();
		if (!empty($this->mysql->error)) {
			throw new \Exception('SQL Error: ' . $this->mysql->error);
		}
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
		}

		return $rows;
	}

	public function selectRow($sql) {

		$result = $this->query($sql);
		$this->query($sql);
		if (!empty($this->mysql->error)) {
			echo $sql . PHP_EOL;
			throw new \Exception('SQL Error: ' . $this->mysql->error);
		}
		$row = $result->fetch_assoc();

		return $row;
	}

	public function escape($value) {
		if (is_null($value)) {
			$escaped = "NULL";
		} elseif (is_numeric($value)) {
			if (is_int($value) || is_float($value)) {
				$escaped = $value;
			} else {
				$escaped = "'$value'";
			}

		} else {
			$escaped = "'" . mysqli_real_escape_string($this->mysql, $value) . "'";
		}
		return $escaped;
	}

	public function insert($table, array $insert, $ignore = false, $update = false) {


		$sql = "INSERT ";
		if ($ignore) {
			$sql .= " IGNORE ";
		}
		$sql .= "INTO {$table} (";

		$fields = array_keys($insert);

		foreach ($fields as $field) {
			$sql .= "`{$field}`, ";
		}
		$sql = substr($sql, 0, -2);
		$sql .= ") VALUES (";
		foreach ($insert as $value) {
			$sql .= $this->escape($value) .', ';
		}
		$sql = substr($sql, 0, -2);
		$sql .= ")";

		if ($update) {
			$sql .= ' ON DUPLICATE KEY UPDATE ';
			foreach ($update as $field => $value) {
				$sql .= "{$field} = {$value}, ";
			}
			$sql = substr($sql, 0, -2);

		}

		$return = $this->query($sql);
		if (!empty($this->mysql->error)) {
			throw new \Exception('SQL Error: ' . $this->mysql->error);
		}
		return $return;

	}

	public function insertMultiple($table, array $values, $ignore = false) {

		$fields = array_keys(current($values));

		$sql = "INSERT ";
		if ($ignore) {
			$sql .= " IGNORE ";
		}
		$sql .= "INTO {$table} (";

		foreach ($fields as $field) {
			$sql .= "`{$field}`, ";
		}
		$sql = substr($sql, 0, -2);
		$sql .= ") VALUES ";
		foreach ($values as $insert) {
			$sql .= "(";
			foreach ($insert as $value) {
				$sql .= $this->escape($value) .', ';
			}
			$sql = substr($sql, 0, -2);
			$sql .= "), ";
		}
		$sql = substr($sql, 0, -2);
		$this->query($sql);
		if (!empty($this->mysql->error)) {
			echo($sql);
			throw new \Exception('SQL Error: ' . $this->mysql->error);
		}


	}

	public function getInClause(array $ins) {
		$sql = ' IN (';
		foreach ($ins as $in) {
			$sql .= $this->escape($in) . ', ';
		}
		$sql = substr($sql, 0, -2);
		$sql .= ' )';
		return $sql;
	}

} 