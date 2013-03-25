<?php


class TasksPresenter extends BasePresenter {

	public function renderList( $experimentId ) {
		$this->template->tasks = $this->getService( 'tasks' )->getTasks( $experimentId );
	}

	public function renderDetail( $id ) {
		$this->template->experimentId = $this->getService( 'tasks' )->getTask( $id )->experiments_id;
		$this->template->taskIds = array( $id );
	}

	public function renderCompare( $id1, $id2 ) {
		$this->template->experimentId = $this->getService( 'tasks' )->getTask( $id1 )->experiments_id;
		$this->template->taskIds = array( $id1, $id2 );
		$this->setView( 'detail' );
	}


}
