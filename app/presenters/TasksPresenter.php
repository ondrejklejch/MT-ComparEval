<?php


class TasksPresenter extends BasePresenter {

	private $tasksModel;

	public function __construct( Tasks $tasksModel ) {
		$this->tasksModel = $tasksModel;
	}

	public function renderList( $experimentId ) {
		$this->template->tasks = $this->tasksModel->getTasks( $experimentId );
	}

	public function renderDetail( $id ) {
		$this->template->experimentId = $this->tasksModel->getTask( $id )->experiments_id;
		$this->template->taskIds = array( $id );
	}

	public function renderCompare( $id1, $id2 ) {
		$this->template->experimentId = $this->tasksModel->getTask( $id1 )->experiments_id;
		$this->template->taskIds = array( $id1, $id2 );
		$this->setView( 'detail' );
	}


}
