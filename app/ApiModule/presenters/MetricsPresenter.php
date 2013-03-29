<?php

namespace ApiModule;

class MetricsPresenter extends \Nette\Application\UI\Presenter {

	public function renderDefault() {
		$metricsModel = $this->getService( 'metrics' );
		
		$response = array();
		$response[ 'metrics' ] = array();
		foreach( $metricsModel->getMetrics() as $metric ) {
			$response[ 'metrics' ][] = $metric->name;
		}

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
