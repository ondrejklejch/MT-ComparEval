<?php


class Tasks {

	private $connection;

	private $tableName;

	public function __construct( \Nette\Database\Connection $connection, $tableName ) {
		$this->connection = $connection;
		$this->tableName = $tableName;
	}


	public function getTask( $id ) {
		return $this->getTable()->where( 'id', $id )->fetch();
	}


	public function getTasksForExperiment( $experimentId ) {
		return $this->getTable()->where( 'experiment_id', $experimentId );
	}


	public function deleteTask( $id ) {
		return $this->getTable()->where( 'id', $id )->delete();
	}


	public function setBleu( $task, $bleu ) {
		$data = array( 
			'bleu' => $bleu,
			'state' => 1,
		);

		return $this->getTable()->where( 'id', $task )->update( $data );

	}


	private function getTable() {
		return $this->connection->table( $this->tableName );
	}

}
