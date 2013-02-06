<?php

class Tasks {

	private $db;

	public function __construct( Nette\Database\Connection $db ) {
		$this->db = $db;
	}

	public function getTasks( $experimentId ) {
		return $this->db->table( 'tasks' )
			->where( 'experiments_id', $experimentId );
	}

	public function saveTask( $name, $experimentId ) {
		$row = $this->db->table( 'tasks' )->insert( array(
			'name' => $name,
			'experiments_id' => $experimentId
		) );

		return $row->getPrimary( TRUE );
	}


}
