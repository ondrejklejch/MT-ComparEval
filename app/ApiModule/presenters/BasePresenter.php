<?php

namespace ApiModule;

class BasePresenter extends \BasePresenter {

	private $httpRequest;

	public function __construct( \Nette\Http\Request $httpRequest ) {
		$this->httpRequest = $httpRequest;
	}

	public function startup() {
		parent::startup();

		if($this->user->isLoggedIn()) {
			return;
		}

		$token = $this->getPostParameter('token', FALSE);
		$usersModel = $this->context->getService('users');
		if (!$existing = $usersModel->findByToken($token)) {
			return;
		}

		if ( in_array( $existing->email, $this->context->parameters["administrators"] ) ) {
			$existing->roles = array( "admin" );
		} else {
			$existing->roles = array();
		}

		$this->user->login(new \Nette\Security\Identity($existing->id, $existing->roles, $existing));
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
