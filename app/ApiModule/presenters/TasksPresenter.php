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
		$parameters = $this->context->getParameters();
		$show_administration = $parameters[ "show_administration" ];

		$response = array();
		$response[ 'tasks' ] = array();
		foreach( $this->tasksModel->getTasks( $experimentId ) as $task ) {
			$taskResponse[ 'id' ] = $task->id;
			$taskResponse[ 'name' ] = $task->name;
			$taskResponse[ 'description' ] = $task->description;
			if( $show_administration ) {
				$taskResponse[ 'edit_link' ] = $this->link( ':tasks:edit', $task->id );
				$taskResponse[ 'delete_link' ] = $this->link( ':tasks:delete', $task->id );
			}

			$response[ 'tasks' ][] = $taskResponse;
		}

		$response[ 'show_administration' ] = $show_administration;

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
