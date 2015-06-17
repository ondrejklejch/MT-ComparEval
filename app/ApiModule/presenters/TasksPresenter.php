<?php

namespace ApiModule;

/**
 * TasksPresenter is used for serving list of task in experiment from REST API
 */
class TasksPresenter extends \Nette\Application\UI\Presenter {

	private $tasksModel;

	public function __construct( \Tasks $tasksModel ) {
		$this->tasksModel = $tasksModel;
	}

	public function renderDefault( $experimentId ) {
		$response = array();
		$response[ 'tasks' ] = array();
		foreach( $this->tasksModel->getTasks( $experimentId ) as $task ) {
			$taskResponse[ 'name' ] = $task->name;
			$taskResponse[ 'id' ] = $task->id;

			$response[ 'tasks' ][] = $taskResponse;
		}

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
