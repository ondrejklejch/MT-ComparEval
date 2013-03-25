<?php

namespace ApiModule;

class TasksPresenter extends \Nette\Application\UI\Presenter {

	public function renderDefault( $experimentId ) {
		$tasksModel = $this->getService( 'tasks' );

		$response = array();
		$response[ 'tasks' ] = array();
		foreach( $tasksModel->getTasks( $experimentId ) as $task ) {
			$taskResponse[ 'name' ] = $task->name;
			$taskResponse[ 'id' ] = $task->id;

			$response[ 'tasks' ][] = $taskResponse;
		}

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
