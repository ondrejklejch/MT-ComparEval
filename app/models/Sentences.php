<?php


class Sentences {

	private $connection;

	public function __construct( \Nette\Database\Connection $connection ) {
		$this->connection = $connection;
	}


	public function getSentencesForTask( $id ) {
		$sql = 'SELECT t.text AS tst, r.text AS ref, s.text AS src, t.diff_bleu AS bleu '
			. 'FROM translation_sentences AS t '
			. 'JOIN source_sentences AS s ON s.position = t.position '
			. 'JOIN reference_sentences AS r ON r.position = t.position '
			. 'JOIN tasks AS ta ON ta.id = t.task_id AND ta.experiment_id = r.experiment_id AND ta.experiment_id = s.experiment_id ' 
			. 'WHERE t.task_id = ?';

		return $this->connection->query( $sql, $id );
	}


	public function getSentencesIdsForTask( $id ) {
		$sql = 'SELECT position AS id FROM translation_sentences '	
			. 'WHERE task_id = ?';

		return $this->connection->query( $sql, $id ); 

	}


	public function getSentencesPairsForTasks( $id1, $id2 ) {
		$sql = 'SELECT t1.text AS tst1, t1.diff_bleu AS bleu1, t2.text AS tst2, t2.diff_bleu AS bleu2, r.text AS ref, s.text AS src '
			. 'FROM translation_sentences AS t1 '
			. 'JOIN source_sentences AS s ON s.position = t1.position '
			. 'JOIN reference_sentences AS r ON r.position = t1.position '
			. 'JOIN translation_sentences AS t2 ON t2.position = t1.position '
			. 'JOIN tasks AS ta ON ta.id = t.task_id AND ta.experiment_id = r.experiment_id '
			. 'WHERE t1.task_id = ? AND t2.task_id = ?';

		return $this->connection->query( $sql, $id1, $id2 );
	}


	public function getTranslationLength( $taskId ) {
		$sql = 'SELECT SUM( length ) AS length FROM translation_sentences '
			. ' WHERE task_id = ?';

		return $this->connection->query( $sql, $taskId )->fetch()->length;
	}



	public function getReferenceLength( $experimentId ) {
		$sql = 'SELECT SUM( length ) AS length FROM reference_sentences '
			. ' WHERE experiment_id = ?';

		return $this->connection->query( $sql, $experimentId )->fetch()->length;
	}


	public function setDiffBleu( $taskId, $position, $bleu ) {
		$data = array(
			'diff_bleu' => $bleu,
		);

		return $this->connection->table( 'translation_sentences' )
			->where( 'task_id', $taskId )
			->where( 'position', $position )
			->update( $data );
	}

}
