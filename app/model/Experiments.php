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
		$row = $this->db->table( 'experiments' )->insert( array(
			'name' => $name,
			'description' => $description
		) );

		return $row->getPrimary( TRUE );
	}

	public function getSentences( $experimentId ) {
		return $this->db->table( 'sentences' )
			->where( 'experiments_id', $experimentId )
			->order( 'id' );
	} 

	public function addSentences( $experimentId, $sentences ) {
		foreach( $sentences as $sentence ) {
			$this->db->table( 'sentences' )->insert( array(
				'experiments_id' => $experimentId,
				'source' => $sentence['source'],
				'reference' => $sentence['reference']
			) );
		}
	}

	public function deleteExperimentByName( $name ) {
		$this->db->table( 'experiments' )
			->where( 'name', $name )
			->delete();
	}

}
