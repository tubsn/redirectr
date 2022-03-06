<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;

class Tracking extends Model
{

	public function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'tracking';

	}

	public function count($id) {
		$this->create([
			'redirect_id' => $id
		]);

	}

	public function table() {
		return $this->db->table;
	}

}
