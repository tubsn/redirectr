<?php

namespace app\models;
use \flundr\database\SQLdb;
use \flundr\mvc\Model;
use \app\models\Chartengine;

class Tracking extends Model
{

	public function __construct() {

		$this->db = new SQLdb(DB_SETTINGS);
		$this->db->table = 'tracking';
		$this->db->primaryIndex = 'redirect_id';
		$this->db->orderby = 'redirect_id';

	}

	public function hits_by_day($id = null, $created = null) {

		$hits = $this->daily_hits($id);
		$daterange = $this->date_range(date('Y-m-d', strtotime('-90 days')),date('Y-m-d'));

		if ($created) {
			$sinceCreated = $this->date_range($created,date('Y-m-d'));
			if (iterator_count($sinceCreated) <= 365) {
				$daterange = $sinceCreated;
			}
		}

		$dates = [];
		foreach ($daterange as $date) {
			$key = $date->format("Y-m-d");
			$dates[$key] = $hits[$key] ?? 0;
		}

		//$dates = array_reverse($dates);
		return $this->to_chart($dates);

	}

	private function to_chart($data) {

		$chart = new ChartEngine();

		$dimensions = $chart->implode(array_keys($data));
		$metric = $chart->implode(array_values($data));

		$options = [
			'metric' => $metric,
			'dimension' => $dimensions,
			'color' => '#db4c73',
			'height' => 400,
			'legend' => 'top',
			'showValues' => false,
			'name' => 'Hits',
			'xfont' => '13px',
			'tickamount' => 15,
			'template' => 'charts/default_bar_chart',
		];


		return $chart->chart_from_scratch($options);

	}

	private function daily_hits($id = null) {

		$table = $this->db->table;

		if (is_null($id)) {

			$SQLstatement = $this->db->connection->prepare(
				"SELECT DATE_FORMAT(date,'%Y-%m-%d'), count(*)
				 FROM $table
				 GROUP BY DATE_FORMAT(date,'%Y-%m-%d')
				 "
			);

			$SQLstatement->execute([':id' => $id]);
			return $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE|\PDO::FETCH_COLUMN);
			
		}

		$SQLstatement = $this->db->connection->prepare(
			"SELECT DATE_FORMAT(date,'%Y-%m-%d'), count(*)
			 FROM $table
			 WHERE `redirect_id` = :id
			 GROUP BY DATE_FORMAT(date,'%Y-%m-%d')
			 "
		);

		$SQLstatement->execute([':id' => $id]);
		return $SQLstatement->fetchAll(\PDO::FETCH_UNIQUE|\PDO::FETCH_COLUMN);

	}

	public function latest_hits($days = 3, $limit = 5) {

		$table = $this->db->table;

		$SQLstatement = $this->db->connection->prepare(
			"SELECT *
			FROM $table
			LEFT JOIN redirects ON redirect_id = redirects.id
			WHERE `date` >= (CURDATE() - INTERVAL $days DAY)
			ORDER BY `date` desc
			LIMIT $limit"
		);

		$SQLstatement->execute();
		return $SQLstatement->fetchAll();

	}

	public function stats($id) {

		$table = $this->db->table;
		$SQLstatement = $this->db->connection->prepare(
			"SELECT date FROM $table WHERE `redirect_id` = :id"
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


	private function date_range($start, $end) {

        $start = new \DateTime($start);
        $end = new \DateTime($end.' +1 day');

        return new \DatePeriod($start, new \DateInterval('P1D'), $end);

	}

}
