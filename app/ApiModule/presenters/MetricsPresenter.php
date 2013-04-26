<?php

namespace ApiModule;

class MetricsPresenter extends \Nette\Application\UI\Presenter {

	private $metricsModel;

	public function __construct( \Metrics $metricsModel ) {
		$this->metricsModel = $metricsModel;
	}


	public function renderDefault() {
		$response = array();
		$response[ 'metrics' ] = array();
		foreach( $this->metricsModel->getMetrics() as $metric ) {
			$response[ 'metrics' ][] = $metric->name;
		}

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderResults( $metric, $task1, $task2 ) {
		$metricId = $this->metricsModel->getMetricsId( $metric );

		$response = array();
		$response[ 'diffs' ] = array();
		$response[ 'diffs' ][ 'name' ] = $metric;
		$response[ 'diffs' ][ 'data' ] = $this->metricsModel->getMetricDiffs( $metricId, $task1, $task2 );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderSamples( $metric, $task1, $task2 ) {
		$metricId = $this->metricsModel->getMetricsId( $metric );

		$response = array();
		$response[ 'samples' ] = array();
		$response[ 'samples' ][ 'name' ] = $metric;
		$response[ 'samples' ][ 'data' ] = $this->metricsModel->getMetricSamples( $metricId, $task1, $task2 );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
