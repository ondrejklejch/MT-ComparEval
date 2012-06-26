<?php


class NGrams {

	/** @var Tasks */
	private $tasksModel;

	private $connection;

	private $sql = '
		SELECT n.sentence_id, n.length, n.nth, n.text FROM translation_ngrams AS n
		WHERE n.task_id = ?
		INTERSECT
		SELECT n.sentence_id, n.length, n.nth, n.text FROM reference_ngrams AS n
		WHERE n.experiment_id = ?
	';


	public function __construct( \Nette\Database\Connection $connection, Tasks $tasks ) {
		$this->connection = $connection;
		$this->tasksModel = $tasks;
	}


	public function getMatchingNGramsCountsByLength( $taskId ) {
		$task = $this->tasksModel->getTask( $taskId );

		$sql = 'SELECT length, COUNT(1) AS count '
			. 'FROM ( ' . $this->sql . ' ) '
			. 'GROUP BY length'; 

		return $this->connection->query( $sql, $taskId, $task->experiment_id );
	}


	public function getMatchingNGramsCountsBySentenceAndLength( $taskId ) {
		$task = $this->tasksModel->getTask( $taskId );

		$sql = 'SELECT sentence_id, length, COUNT(1) AS count '
			. 'FROM ( ' . $this->sql . ' ) '
			. 'GROUP BY sentence_id, length'; 

		$ngrams = $this->connection->query( $sql, $taskId, $task->experiment_id );
		$ngramsBySentence = array();
		foreach( $ngrams as $ngram ) {
			$ngramsBySentence[ $ngram->sentence_id ][ $ngram->length ] = $ngram->count;
		}		

		return $ngramsBySentence;
	}


	public function getTranslationNGramsCountsByLength( $taskId ) {
		$sql = 'SELECT length, COUNT(1) AS count FROM translation_ngrams '
			. 'WHERE task_id = ? '
			. 'GROUP BY length';

		return $this->connection->query( $sql, $taskId );
	}


	public function getTranslationNGramsCountsBySentenceAndLength( $taskId ) {
		$sql = 'SELECT sentence_id, length, COUNT(1) AS count FROM translation_ngrams '
			. 'WHERE task_id = ? '
			. 'GROUP BY sentence_id, length';

		$ngrams = $this->connection->query( $sql, $taskId );
		$ngramsBySentence = array();
		foreach( $ngrams as $ngram ) {
			$ngramsBySentence[ $ngram->sentence_id ][ $ngram->length ] = (int) $ngram->count;
		}		

		return $ngramsBySentence;
	}



}
