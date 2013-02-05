<?php


class Experiments {

	private $db;

	public function __construct( Nette\Database\Connection $db ) {
		$this->db = $db;
	}


	public function getExperiments() {
		return $this->db->table( 'experiments' );
	}


	public function saveExperiment( $name, $description = "" ) {
		$this->db->table( 'experiments' )->insert( array(
			'name' => $name,
			'description' => $description
		) );
	}



}
