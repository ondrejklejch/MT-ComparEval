<?php


class Tasks {

	private $table;

	public function __construct( \Nette\Database\Table\Selection $table ) {
		$this->table = $table;
	}


	public function getTasksForExperiment( $experimentId ) {
		return $this->table->where( 'experiment_id', $experimentId );
	}

}
