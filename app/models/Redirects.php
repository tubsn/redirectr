<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;

class Redirects extends Model
{

	public $defaultURL = '/';

	public function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'redirects';
		$this->tracking = new Tracking();

	}

	public function stats($id) {
		return $this->entry_with_tracking($id);
	}

	public function list() {
		return $this->entries_with_tracking();
	}

	public function url($shortURL) {
		$redirect = $this->url_from_short($shortURL);

		if ($redirect) {
			$this->tracking->track($redirect['id']);
			return $redirect['url'];
		}

		return $this->defaultURL;
	}


	private function url_from_short($shortURL) {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT id,url FROM $table WHERE `shorturl` = :shortURL"
		);

		$SQLstatement->execute([':shortURL' => $shortURL]);
		return $SQLstatement->fetch();

	}

	private function entry_with_tracking($id) {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT * FROM $table WHERE id = :id"
		);
		$SQLstatement->execute([':id' => $id]);
		$redirect = $SQLstatement->fetch();

		$hits = $this->tracking->stats($id);
		$redirect['hits'] = count($hits);
		$redirect['events'] = $hits;

		return $redirect;

	}

	private function entries_with_tracking($filter = null) {

		$table = $this->db->table;
		$trackingTable = $this->tracking->table();

		if (is_null($filter)) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT id,shorturl,url,created, COUNT(redirect_id) as hits
				 FROM $table LEFT JOIN $trackingTable ON redirect_id = id
				 GROUP BY id"
			);
			$SQLstatement->execute();

		}

		else {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT id,shorturl,url,created, COUNT(redirect_id) as hits
				 FROM $table LEFT JOIN $trackingTable ON redirect_id = id
				 WHERE `shorturl` = :filter
				 GROUP BY id"
			);
			$SQLstatement->execute([':filter' => $filter]);

		}

		return $SQLstatement->fetchALL(\PDO::FETCH_UNIQUE);

	}


}
