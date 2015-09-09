<?php

namespace ApiModule;

class ExperimentsPresenter extends BasePresenter {

	private $model;

	public function __construct( \Nette\Http\Request $httpRequest, \Experiments $model ) {
		parent::__construct( $httpRequest );
		$this->model = $model;
	}

	public function renderUpload() {
		$name = $this->getPostParameter( 'name' );
		$url_key = \Nette\Utils\Strings::webalize( $name );
		$description = $this->getPostParameter( 'description' );
		$source = $this->getPostFile( 'source' );
		$reference = $this->getPostFile( 'reference' );

		$data = array(
			'name' => $name,
			'description' => $description,
			'url_key' => $url_key
		);

		$path = __DIR__ . '/../../../data/' . $url_key . '/';
		$source->move( $path . 'source.txt' );
		$reference->move( $path . 'reference.txt' );
		file_put_contents( $path . 'config.neon', "name: $name\ndescription: $description\nurl_key: $url_key" );

		$response = array( 'experiment_id' => $this->model->saveExperiment( $data ) );

		if ( $this->getPostParameter( 'redirect', False ) ) {
			$this->flashMessage( "Experiment was successfully uploaded. It will appear in this list once it is imported.", "success" );
			$this->redirect( ":Experiments:list" );
		} else {
			$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
		}
	}

	public function renderStatus( $id ) {
		$experiment = $this->model->getExperimentById( $id );
		$tasks = $experiment->related( 'tasks' );
		$allTasksImported = array_reduce( $tasks->fetchAll(), function( $acc, $cur ) { return $acc && $cur->visible == 1; }, TRUE );

		$response = array(
			'experiment_imported' => @$experiment->visible == 1,
			'all_tasks_imported' => $allTasksImported,
			'url' => $this->link( '//:Tasks:list', $id ),
		);

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

	public function renderDelete( $id ) {
		$response = array( 'status' => (bool) $this->model->deleteExperiment( $id ) );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
