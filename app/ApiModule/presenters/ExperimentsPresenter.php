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
		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
