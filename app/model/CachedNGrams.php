<?php

/**
 * CachedNGrams decorates NGrams and adds support for caching n-grams
 *
 * N-grams are cached because theit computation is rather expensive
 */
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

			$this->getImproving( $taskId, $task->id, true );
			$this->getWorsening( $taskId, $task->id, true );
		}
	}

	public function getImproving( $task1, $task2, $regenerateCache = false ) {
		$key = $this->getCacheKey( 'improving', $task1, $task2 );

		if( $this->cache->load( $key ) === NULL && !$regenerateCache ) {
			return array();
		}

		return $this->cache->load( $key, function() use ( $task1, $task2 ) {
			return parent::getImproving( $task1, $task2 );
		} );
	}

	public function getWorsening( $task1, $task2, $regenerateCache = false ) {
		$key = $this->getCacheKey( 'worsening', $task1, $task2 );

		if( $this->cache->load( $key ) === NULL && !$regenerateCache ) {
			return array();
		}

		return $this->cache->load( $key, function() use ( $task1, $task2 ) {
			return $worsening = parent::getWorsening( $task1, $task2 );
		} );
	}

	private function getCacheKey( $type, $task1, $task2 ) {
		return join( '-', array( $type, min( $task1, $task2 ), max( $task1, $task2 ) ) );
	}

}
