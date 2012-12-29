<?php


class Sentences {

	private $db;

	public function __construct( Nette\Database\Connection $db ) {
		$this->db = $db;
	}

	public function getDb() {
		return $this->db;
	}


	public function getSentencesCount( $tasksIds ) {
		return $this->db->table( 'translations' )
			->where( 'tasks_id', $tasksIds )
			->count( 'DISTINCT sentences_id' );
	}

	public function getSentences( $taskIds, $offset, $limit, $orderBy, $order ) {
		$sentenceIds = $this->getSentenceIdsForRequest( $taskIds, $offset, $limit, $orderBy, $order );
		
		return $this->getSentencesWithIds( $sentenceIds, $taskIds );
	}

	public function getSentencesWithIds( $sentenceIds, $taskIds ) {
		$rows = array();
		foreach( $this->db->table( 'sentences' )->where( 'id', $sentenceIds ) as $sentence ) {
			$row = array();
			$row[ 'sentence_id' ] = $sentence[ 'id' ];
			$row[ 'source' ] = $sentence[ 'source' ];
			$row[ 'reference' ] = $sentence[ 'reference' ];
			$row[ 'translations' ] = array();
			
			foreach( $sentence->related( 'translations.sentences_id' )->where( 'tasks_id', $taskIds ) as $translation ) {
				$rowTranslation = array();
				$rowTranslation[ 'task_id' ] = $translation[ 'tasks_id' ];
				$rowTranslation[ 'text' ] = $translation[ 'text' ];
				$rowTranslation[ 'metrics' ] = array();

				foreach( $translation->related( 'translations_metrics.translations_id' ) as $metric ) {
					$name = $this->db->table( 'metrics' )->find( $metric['metrics_id'] )->fetch()->name;
					$rowTranslation[ 'metrics' ][ $name ] = $metric[ 'score' ];
				}

				$row[ 'translations' ][] = $rowTranslation;
			}

			$rows[] = $row;
		}

		return $rows;
	}


	private function getSentenceIdsForRequest( $taskIds, $offset, $limit, $orderBy, $order ) {
		if( $orderBy === 'id' ) {
			$result = $this->db
				->table( 'translations' )
				->where( 'tasks_id', $taskIds ) 
				->order( 'sentences_id' )
				->limit( $limit, $offset )
				->fetchPairs( 'sentences_id' );
		} else {
			$metricsId = $this->db
				->table( 'metrics' )
				->where( 'name', $orderBy )
				->fetch()->id;

			$result = $this->db
				->table( 'translations' )
				->where( 'tasks_id', $taskIds ) 
				->where( 'translations_metrics:metrics_id', $metricsId )
				->order( 'translations_metrics:score ' . strtoupper( $order ) )
				->limit( $limit, $offset )
				->fetchPairs( 'sentences_id' );
		}			

		return array_keys( $result );
	}



}
