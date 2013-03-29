<?php


class Metrics {

	private $db;

	public function __construct( Nette\Database\Connection $db ) {
		$this->db = $db;
	}

	public function getMetrics() {
		return $this->db
			->table( 'metrics' )
			->order( 'name' );
	}


	public function getMetricsId( $name ) {
		return  $this->db
			->table( 'metrics' )
			->where( 'name', $name )
			->fetch()->id;
	}
}
