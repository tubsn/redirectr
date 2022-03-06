<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;

class Tracking extends Model
{

	public function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'tracking';
		$this->db->primaryIndex = 'redirect_id';
		$this->db->orderby = 'redirect_id';

	}

	public function stats($id) {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT DATE_FORMAT(date,'%Y-%m-%d') FROM $table WHERE `redirect_id` = :id"
		);

		$SQLstatement->execute([':id' => $id]);
		return $SQLstatement->fetchAll(\PDO::FETCH_COLUMN);

	}

	public function track($id) {
		$this->create([
			'redirect_id' => $id
		]);

	}

	public function table() {
		return $this->db->table;
	}

}
