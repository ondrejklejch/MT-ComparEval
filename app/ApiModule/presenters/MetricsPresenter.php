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


	public function renderResults( $metric, $task1, $task2 ) {
		$metricsModel = $this->getService( 'metrics' );
		$metricId = $metricsModel->getMetricsId( $metric );

		$response = array();
		$response[ 'diffs' ] = array();
		$response[ 'diffs' ][ 'name' ] = $metric;
		$response[ 'diffs' ][ 'data' ] = $metricsModel->getMetricDiffs( $metricId, $task1, $task2 );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderSamples( $metric, $task1, $task2 ) {
		$metricsModel = $this->getService( 'metrics' );
		$metricId = $metricsModel->getMetricsId( $metric );

		$response = array();
		$response[ 'samples' ] = array();
		$response[ 'samples' ][ 'name' ] = $metric;
		$response[ 'samples' ][ 'data' ] = $metricsModel->getMetricSamples( $metricId, $task1, $task2 );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
