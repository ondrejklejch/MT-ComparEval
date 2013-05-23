<?php

use Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\CliRouter,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	private $consoleMode = false;

	public function __construct( $consoleMode ) {
		$this->consoleMode = $consoleMode;
	}

	/**
	 * @return Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		if( $this->consoleMode ) {
			$router[] = new CliRouter(); 
		} else {
			$router[] = new Route('index.php', 'Experiments:list', Route::ONE_WAY);
			$router[] = new Route('api/sentences', 'Api:Sentences:default');
			$router[] = new Route('api/sentences/by-id', 'Api:Sentences:byId');
			$router[] = new Route('api/tasks', 'Api:Tasks:default');
			$router[] = new Route('api/metrics', 'Api:Metrics:default');
			$router[] = new Route('api/metrics/scores', 'Api:Metrics:scores');
			$router[] = new Route('api/metrics/scores-in-experiment', 'Api:Metrics:scoresInExperiment');
			$router[] = new Route('api/metrics/results', 'Api:Metrics:results');
			$router[] = new Route('api/metrics/samples', 'Api:Metrics:samples');
			$router[] = new Route('api/metrics/samples-diff', 'Api:Metrics:samplesDiff');
			$router[] = new Route('api/ngrams/confirmed', 'Api:NGrams:confirmed');
			$router[] = new Route('api/ngrams/unconfirmed', 'Api:NGrams:unconfirmed');
			$router[] = new Route('tasks/<id1>-<id2>/compare', 'Tasks:compare');
			$router[] = new Route('tasks/<id>/detail', 'Tasks:detail');
			$router[] = new Route('<presenter>/<action>[/<id>]', 'Experiments:list');
		}		

		return $router;
	}

}
