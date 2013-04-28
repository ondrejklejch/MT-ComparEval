<?php


class CachedNGrams extends NGrams {

	private $cache;

	private $tasksModel;

	public function __construct( Nette\Database\Connection $db, Nette\Caching\Cache $cache, Tasks $tasksModel ) {
		parent::__construct( $db );

		$this->cache = $cache;
		$this->tasksModel = $tasksModel;
	}

	public function precomputeNgrams( $experimentId, $taskId ) {
		$tasks = $this->tasksModel->getTasks( $experimentId );

		foreach( $tasks as $task ) {
			if ( $task->id == $taskId ) {
				continue;
			}

			$this->getImproving( $taskId, $task->id );
			$this->getWorsening( $taskId, $task->id );
		}
	}

	public function getImproving( $task1, $task2 ) {
		$key = $this->getCacheKey( 'improving', $task1, $task2 );
		$improving = $this->cache->load( $key );
		if ( $improving === NULL ) {
			$improving = parent::getImproving( $task1, $task2 );
			$this->cache->save( $key, $improving );
		}

		return $improving;
	}

	public function getWorsening( $task1, $task2 ) {
		$key = $this->getCacheKey( 'worsening', $task1, $task2 );
		$worsening = $this->cache->load( $key );
		if ( $worsening === NULL ) {
			$worsening = parent::getWorsening( $task1, $task2 );
			$this->cache->save( $key, $worsening );
		}

		return $worsening;
	}

	private function getCacheKey( $type, $task1, $task2 ) {
		return join( '-', array( $type, min( $task1, $task2 ), max( $task1, $task2 ) ) );
	}

}
