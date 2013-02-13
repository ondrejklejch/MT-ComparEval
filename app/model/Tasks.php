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

	public function saveTask( $data ) {
		$row = $this->db->table( 'tasks' )->insert( $data );

		return $row->getPrimary( TRUE );
	}

	public function addSentences( $taskId, $sentences ) {
		foreach( $sentences as $sentence ) {
			$data = array(
				'sentences_id' => $sentence['experiment']['id'],
				'tasks_id' => $taskId,
				'text' => $sentence['translation']
			);

			$this->db->table( 'translations' )->insert( $data );
		}
	}

	public function deleteTaskByName( $experimentId, $name ) {
		$this->db->table( 'tasks' )
			->where( 'experiments_id', $experimentId )
			->where( 'url_key', $name )
			->delete();
	}


}
