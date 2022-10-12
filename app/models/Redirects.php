<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \flundr\auth\Auth;
use \flundr\controlpanel\models\User;

class Redirects extends Model
{

	public $defaultURL = 'https://www.lr-online.de';

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

	public function list($category = null, $offset = null, $limit = null) {
		return $this->entries_with_tracking(null, $category, $offset, $limit);
	}

	public function find($filter = null, $offset = null, $limit = null) {
		return $this->entries_with_tracking($filter, null, $offset, $limit);
	}

	public function most_clicks($offset = null, $limit = null) {
		return $this->entries_with_tracking(null, null, $offset, $limit, 'hits');
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
			$url = $this->add_get_parameters_from_source($redirect['url']);
			$url = html_entity_decode($url);
			$this->tracking->track($redirect['id']);
			return $url;
		}

		return $this->defaultURL;
	}

	public function count($category = null) {
		$table = $this->db->table;

		if ($category) {
			$SQLstatement = $this->db->connection->prepare(
				"SELECT count(*) as items FROM $table WHERE `category` = :category"
			);

			$SQLstatement->execute([':category' => $category]);
			return $SQLstatement->fetch()['items'];
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT count(*) as items FROM $table"
		);

		$SQLstatement->execute();
		return $SQLstatement->fetch()['items'];

	}

	public function category_stats($category = null) {

		$table = $this->db->table;
		$trackingTable = $this->tracking->table();

		if ($category) {
			$SQLstatement = $this->db->connection->prepare(
				"SELECT
					(SELECT count(*) FROM $table WHERE `category` = :category) as redirects,
					(SELECT count(*) FROM $trackingTable LEFT JOIN $table ON redirect_id = id WHERE `category` = :category) as hits"
			);

			$SQLstatement->execute([':category' => $category]);
			return $SQLstatement->fetch();
		}

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
			"SELECT id,shorturl,url,utm FROM $table WHERE `shorturl` = :shortURL"
		);

		$SQLstatement->execute([':shortURL' => $shortURL]);
		$urlData = $SQLstatement->fetch();

		if (empty($urlData)) {return null;}
		$urlData = $this->auto_utm_parameters($urlData);

		return $urlData;

	}

	public function auto_utm_parameters($data) {

		if (!$data['utm']) {return $data;}

		$additionalParams = [
			'utm_source' => 'redirectr',
			'utm_medium' => 'shortlink',
			'utm_campaign' => $data['shorturl'],
		];
		$data['rawurl'] = $data['url'];
		$data['url'] .= (strpos($data['url'], '?') ? '&' : '?') . http_build_query($additionalParams);

		return $data;
	}

	public function add_get_parameters_from_source($destinationURL) {
		if (empty($_GET)) {return $destinationURL;}
		$destinationURL .= (strpos($destinationURL, '?') ? '&' : '?') . http_build_query($_GET);
		return $destinationURL;
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

	private function entries_with_tracking($filter = null, $category = null, $offset = null, $limit = null, $orderBy = 'created') {

		if (!$offset) {$offset = 0;}
		if (!$limit) {$limit = 1000;}

		$table = $this->db->table;
		$trackingTable = $this->tracking->table();

		$preparedVariables = [];
		$whereQuerys = [];
		$whereString = '';
		$having = '';

		if ($filter) {
			array_push($whereQuerys, "`shorturl` LIKE :filter");
			$preparedVariables[':filter'] = '%%'.$filter . '%%';
		}

		if ($category) {
			array_push($whereQuerys, "`category` = :category");
			$preparedVariables[':category'] = $category;
		}

		if ($orderBy == 'hits') {
			$having = "HAVING `hits` >= 3";
		}

		if (count($whereQuerys)>0) {
			$whereQuerys = implode(' AND ', $whereQuerys);
			$whereString = 'WHERE ' . $whereQuerys;
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT id,shorturl,category,url,url as rawurl,utm,created, COUNT(redirect_id) as hits
			 FROM $table LEFT JOIN $trackingTable ON redirect_id = id
			 $whereString
			 GROUP BY id 
			 $having
			 ORDER BY $orderBy DESC LIMIT $offset, $limit"
		);

		$SQLstatement->execute($preparedVariables);
		$entries = $SQLstatement->fetchALL(\PDO::FETCH_UNIQUE);

		$entries = array_map([$this, 'auto_utm_parameters'], $entries);

		return $entries;

	}

}
