<?php


class TasksPresenter extends BasePresenter {

	private $tasksModel;

	private $experimentsModel;

	public function __construct( Tasks $tasksModel, Experiments $experimentsModel ) {
		$this->tasksModel = $tasksModel;
		$this->experimentsModel = $experimentsModel;
	}

	public function renderList( $experimentId ) {
		$this->template->experimentId = $experimentId;
		$this->template->experiment = $this->experimentsModel->getExperimentById( $experimentId );
		$this->template->tasks = $this->tasksModel->getTasks( $experimentId );
	}

	public function renderCompare( $id1, $id2 ) {
		$this->template->experimentId = $this->tasksModel->getTask( $id1 )->experiments_id;
		$this->template->taskIds = array( $id1, $id2 );
	}

	public function actionDelete( $taskId ) {
		$experimentId = $this->tasksModel->getTask( $taskId )->experiments_id;
		$this->tasksModel->deleteTask( $taskId );

		$this->redirect( 'list', $experimentId );
	}


}
