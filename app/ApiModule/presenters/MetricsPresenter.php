<?php

namespace ApiModule;

/**
 * MetricsPresenter is used for serving metrics data from REST API
 *
 * This data is used on frontend for rendering various charts and tables
 */
class MetricsPresenter extends \Nette\Application\UI\Presenter {

	private $metricsModel;

	private $tasksModel;

	public function __construct( \Metrics $metricsModel, \Tasks $tasksModel ) {
		$this->metricsModel = $metricsModel;
		$this->tasksModel = $tasksModel;
	}


	public function renderDefault() {
		$response = array();
		$response[ 'metrics' ] = array();
		foreach( $this->metricsModel->getMetrics() as $metric ) {
			$response[ 'metrics' ][] = $metric->name;
		}

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderScores( $task1, $task2 ) {
		$response = array();
		$response[ $task1 ] = $this->tasksModel->getTaskMetrics( $task1 );
		$response[ $task2 ] = $this->tasksModel->getTaskMetrics( $task2 ); 

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderScoresInExperiment( $experiment ) {
		$tasks = array();
		$metrics = array();

		foreach( $this->metricsModel->getMetrics() as $metric ) {
			$metrics[ $metric->name ] = array(
				'name' => $metric->name,
				'data' => array()
			);
		}

		foreach( $this->tasksModel->getTasks( $experiment ) as $task ) {
			$tasks[] = $task->name;
			
			foreach( $this->tasksModel->getTaskMetrics( $task ) as $name => $score ) {
				$metrics[ $name ][ 'data' ][] = $score;
			}
		}

		$metrics = array_filter( $metrics, function( $metric ) {
			return strpos( $metric[ 'name' ], '-cis' ) === FALSE;
		} );

		$response = array(
			'tasks' => $tasks,
			'metrics' => array_values( $metrics )
		);

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


	public function renderSamples( $metric, $task ) {
		$metricId = $this->metricsModel->getMetricsId( $metric );

		$response = array();
		$response[ 'samples' ] = array();
		$response[ 'samples' ][ 'name' ] = $metric;
		$response[ 'samples' ][ 'data' ] = $this->metricsModel->getMetricSamples( $metricId, $task );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}


	public function renderSamplesDiff( $metric, $task1, $task2 ) {
		$metricId = $this->metricsModel->getMetricsId( $metric );

		$response = array();
		$response[ 'samples' ] = array();
		$response[ 'samples' ][ 'name' ] = $metric;
		$response[ 'samples' ][ 'data' ] = $this->metricsModel->getMetricSamplesDiff( $metricId, $task1, $task2 );

		$this->sendResponse( new \Nette\Application\Responses\JsonResponse( $response ) );
	}

}
