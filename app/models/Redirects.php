<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\auth\Auth;
use \flundr\controlpanel\models\User;

class Redirects extends Model
{

	public $defaultURL = '/';

	public function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'redirects';
		$this->tracking = new Tracking();

	}

	public function details($id) {

		$entry = $this->entry_with_tracking($id);

		$userDB = new User;
		$creator = $userDB->get($entry['created_by'],['firstname']);
		$editor = $userDB->get($entry['edited_by'],['firstname']);
		if ($creator) {$entry['created_by_name'] = implode(' ', $creator);}
		if ($editor) {$entry['edited_by_name'] = implode(' ', $editor);}

		return $entry;
	}

	public function list($filter = null) {
		return $this->entries_with_tracking($filter);
	}



	public function new($data) {

		$data = $this->sanitize_urls($data);
		$data['created_by'] = Auth::get('id');

		try {
			$this->create($data);
		} catch (\PDOException $e) {

			if ($e->errorInfo[1] == 1062) {
				throw new \Exception("Achtung: KurzURL ist Bereits vergeben", 1062);
			}

			throw new \Exception("Fehler beim Speichern: " . $e->getMessage(), 404);

		}

	}


	public function set($data, $id) {

		$data = $this->sanitize_urls($data);
		$data['edited_by'] = Auth::get('id');

		try {
			$this->update($data,$id);
		} catch (\PDOException $e) {

			if ($e->errorInfo[1] == 1062) {
				throw new \Exception("Achtung: KurzURL ist Bereits vergeben", 1062);
			}
			throw new \Exception("Fehler beim Speichern", 404);

		}

	}

	public function remove_tracking($id) {
		$this->tracking->delete($id);
	}

	private function sanitize_urls($data) {

		if (empty($data['shorturl'])) {throw new \Exception("Bitte Kurzlink ausfüllen", 400);}
		if (empty($data['url'])) {throw new \Exception("Bitte ZielURL ausfüllen", 400);}

		$data['url'] = $this->trim_input($data['url']);
		$data['shorturl'] = $this->trim_input($data['shorturl']);

		$data['url'] = $this->prefixHTTP($data['url']);
		$data['shorturl'] = $this->remove_trailing_slash($data['shorturl']);
		if ($this->has_slashes($data['shorturl'])) {
			throw new \Exception("bitte keine Verschachtelten URLs z.B. /lausitz/cottbus", 400);
		}
		return $data;
	}


	private function trim_input($url) {
		return trim($url);
	}

	private function has_slashes($url) {
		if (count(explode('/', $url)) > 1) {return true;}
		return false;
	}

	private function remove_trailing_slash($url) {
		return ltrim($url, '/');
	}

	private function prefixHTTP($url) {
		return preg_replace('/^(?!https?:\/\/)/', 'http://', $url);
	}

	public function url($shortURL) {
		$redirect = $this->url_from_short($shortURL);

		if ($redirect) {
			$this->tracking->track($redirect['id']);
			return $redirect['url'];
		}

		return $this->defaultURL;
	}

	public function global_stats() {

		$table = $this->db->table;
		$trackingTable = $this->tracking->table();
		$SQLstatement = $this->db->connection->prepare(
			"SELECT
				(SELECT count(*) FROM $table) as redirects,
				(SELECT count(*) FROM $trackingTable) as hits"
		);

		$SQLstatement->execute();
		return $SQLstatement->fetch();

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

		if (empty($redirect)) {return null;}

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
				 GROUP BY id ORDER BY created DESC"
			);
			$SQLstatement->execute();

		}

		else {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT id,shorturl,url,created, COUNT(redirect_id) as hits
				 FROM $table LEFT JOIN $trackingTable ON redirect_id = id
				 WHERE `shorturl` LIKE :filter
				 GROUP BY id ORDER BY created DESC"
			);
			$SQLstatement->execute([':filter' => '%%'.$filter . '%%']);

		}

		return $SQLstatement->fetchALL(\PDO::FETCH_UNIQUE);

	}


}
