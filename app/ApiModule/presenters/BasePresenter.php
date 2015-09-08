<?php

namespace ApiModule;

class BasePresenter extends \Nette\Application\UI\Presenter {

	private $httpRequest;

	public function __construct( \Nette\Http\Request $httpRequest ) {
		$this->httpRequest = $httpRequest;
	}

	protected function getPostParameter( $name, $sendError = TRUE ) {
		if ( !$this->httpRequest->getPost( $name ) && $sendError ) {
			return $this->sendResponse( new \Nette\Application\Responses\JsonResponse( array( 'error' => "Missing field $name" ) ) );
		}

		return $this->httpRequest->getPost( $name );
	}

	protected function getPostFile( $name ) {
		if ( !$this->httpRequest->getFile( $name ) ) {
			return $this->sendResponse( new \Nette\Application\Responses\JsonResponse( array( 'error' => "Missing file $name" ) ) );
		}

		return $this->httpRequest->getFile( $name );
	}
}
