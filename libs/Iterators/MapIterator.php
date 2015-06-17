<?php

/**
 * MapIterator is same as haskell map :: (a->b)->[a]->[b]
 */
class MapIterator extends IteratorIterator {

	private $callback;

	public function __construct( Traversable $iterator, $callback ) {
		parent::__construct( $iterator );

		$this->callback = $callback;
	}

	public function current() {
		$callback = $this->callback;

		return $callback( parent::current() );
	}

}
