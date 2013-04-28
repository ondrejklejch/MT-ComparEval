<?php


class CachedNGrams extends NGrams {

	private $cache;

	public function __construct( Nette\Database\Connection $db, Nette\Caching\Cache $cache ) {
		parent::__construct( $db );

		$this->cache = $cache;
	}

	public function getImproving( $task1, $task2 ) {
		$key = $this->getCacheKey( 'improving', $task1, $task2 );
		$improving = $this->cache->load( $key );
		if ( $improving === NULL ) {
			$improving = parent::getWorsening( $task1, $task2 );
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
		return array( $type, min( $task1, $task2 ), max( $task1, $task2 ) );
	}

}
