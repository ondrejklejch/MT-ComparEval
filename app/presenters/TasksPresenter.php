<?php


class TasksPresenter extends BasePresenter {

	/** @var Tasks */
	private $tasksModel;

	/** @var Sentences */
	private $sentencesModel;


	public function startup() {
		parent::startup();

		$this->tasksModel = $this->getService( 'tasksModel' );
		$this->sentencesModel = $this->getService( 'sentencesModel' );
	}


	public function renderDetail( $id ) {
		$this->template->task = $this->tasksModel->getTask( $id );
		$this->template->sentences = $this->sentencesModel->getSentencesForTask( $id );
	}


	public function renderCompare( $id1, $id2 ) {
		$this->template->task1 = $this->tasksModel->getTask( $id1 );
		$this->template->task2 = $this->tasksModel->getTask( $id2 );
		$this->template->sentences = $this->sentencesModel->getSentencesPairsForTasks( $id1, $id2 );
	}


	public function actionDelete( $id ) {
		$task = $this->tasksModel->getTask( $id );
		$this->tasksModel->deleteTask( $id );
		$this->flashMessage( sprintf( "Task %s was successfully deleted.", $task['name'] ) );

		$this->redirect( 'Experiments:detail', $task['experiment_id'] );
	}

}
