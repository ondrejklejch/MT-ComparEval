<?php


class Experiments {

	private $db;

	public function __construct( Nette\Database\Connection $db ) {
		$this->db = $db;
	}

	public function getExperiments() {
		return $this->db->table( 'experiments' );
	}

	public function getExperimentByName( $name ) {
		return $this->db->table( 'experiments' )
			->where( 'url_key', $name )
			->fetch();
	}

	public function saveExperiment( $data ) {
		$row = $this->db->table( 'experiments' )->insert( $data );

		return $row->getPrimary( TRUE );
	}

	public function getSentences( $experimentId ) {
		return $this->db->table( 'sentences' )
			->where( 'experiments_id', $experimentId )
			->order( 'id' );
	} 

	public function addSentences( $experimentId, $sentences ) {
		$this->db->beginTransaction();

		foreach( $sentences as $sentence ) {
			$this->db->table( 'sentences' )->insert( array(
				'experiments_id' => $experimentId,
				'source' => $sentence['source'],
				'reference' => $sentence['reference']
			) );
		}

		$this->db->commit();
	}

	public function deleteExperimentByName( $name ) {
		$this->db->table( 'experiments' )
			->where( 'url_key', $name )
			->delete();
	}

}
