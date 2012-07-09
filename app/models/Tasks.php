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


	public function createTask( $values ) {
		$task = $this->getTable()->insert( $this->prepareTask( $values ) );

		$this->processTranslation( $task->getPrimary(), $values[ 'translation' ] );
	}


	private function processTranslation( $id, $translationFile ) {
		$script = 'scripts/translation/process_translation.sh';
		$database = 'app/database';		
		$file = $translationFile->getPathname();

		`sh $script $file $id $database`;
	}


	public function setBleu( $task, $bleu ) {
		$data = array( 
			'bleu' => $bleu,
			'state' => 1,
		);

		return $this->getTable()->where( 'id', $task )->update( $data );

	}


	private function prepareTask( $values ) {
		return array(
			'name' => $values[ 'name' ],
			'comment' => $values[ 'comment' ],
			'experiment_id' => $values[ 'experiment_id' ],
		);
	}

	private function getTable() {
		return $this->connection->table( $this->tableName );
	}

}
