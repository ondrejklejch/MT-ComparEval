<?php

namespace ApiModule;

class NGramsPresenter extends \Nette\Application\UI\Presenter {

	private $ngramsModel;

	private $cache;

	public function __construct( \NGrams $ngramsModel, \Nette\Caching\Cache $cache ) {
		$this->ngramsModel = $ngramsModel;
		$this->cache = $cache;
	}


	public function renderConfirmed( $task1, $task2 ) {
		$key = $this->getCacheKey( 'confirmed', $task1, $task2 );
		$response = $this->cache->load( $key );
		if ( $response === NULL ) {
			$response = $this->ngramsModel->getImproving( $task1, $task2 );
			$this->cache->save( $key, $response );
		}

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderUnconfirmed( $task1, $task2 ) {
		$key = $this->getCacheKey( 'unconfirmed', $task1, $task2 );
		$response = $this->cache->load( $key );
		if ( $response === NULL ) {
			$response = $this->ngramsModel->getWorsening( $task1, $task2 );
			$this->cache->save( $key, $response );
		}

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	private function getCacheKey( $type, $task1, $task2 ) {
		return array( $type, min( $task1, $task2 ), max( $task1, $task2 ) );
	}

}
