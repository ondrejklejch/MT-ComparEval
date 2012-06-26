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


	public function getTranslationNGramsCountsByLength( $taskId ) {
		$sql = 'SELECT length, COUNT(1) AS count FROM translation_ngrams '
			. ' WHERE task_id = ? '
			. ' GROUP BY length';

		return $this->connection->query( $sql, $taskId );
	}



}
