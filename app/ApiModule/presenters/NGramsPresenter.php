<?php

namespace ApiModule;

/**
 * NGramsPresenter is used for serving top improving/worsening n-grams from REST API
 */
class NGramsPresenter extends \Nette\Application\UI\Presenter {

	private $ngramsModel;

	public function __construct( \NGrams $ngramsModel ) {
		$this->ngramsModel = $ngramsModel;
	}


	public function renderConfirmed( $task1, $task2 ) {
		$response = $this->ngramsModel->getImproving( $task1, $task2 );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderUnconfirmed( $task1, $task2 ) {
		$response = $this->ngramsModel->getWorsening( $task1, $task2 );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
