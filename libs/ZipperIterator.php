<?php

class ZipperIterator implements Iterator {

	private $iterators;
	private $position;
	private $reportLengthsMismatch;

	public function __construct( array $iterators, $reportLengthsMismatch = FALSE ) {
		$this->assertAllIterators( $iterators );
		$this->assertSameLengths( $iterators, $reportLengthsMismatch );

		$this->iterators = $iterators;
		$this->reportLengthsMismatch = $reportLengthsMismatch;
	}

	private function assertAllIterators( $iterators ) {
		array_walk( $iterators, function( $iterator ) {
			if( !$iterator instanceof Iterator ) {
				throw new InvalidArgumentException( 'Given value is not an iterator.' );
			}
		} );
	} 

	private function assertSameLengths( $iterators, $reportLengthsMismatch ) {
		$lengths = array_map( function( $iterator ) {
			return iterator_count( $iterator );
		}, $iterators );

		$sameLengths = count( array_unique( $lengths ) ) <= 1;
		if( !$sameLengths && $reportLengthsMismatch ) {
			throw new IteratorsLengthsMismatchException( 'Zipped iterators lengths are not equal' );
		} 
	}

	public function current() {
		return array_map( function( $iterator ) {
			return $iterator->current();
		}, $this->iterators );
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		array_walk( $this->iterators, function( $iterator ) {
			$iterator->next();
		} );
		$this->position++;
	}

	public function rewind() {
		array_walk( $this->iterators, function( $iterator ) {
			$iterator->rewind();
		} );
		$this->position = 0;
	}

	public function valid() {
		return array_reduce( $this->iterators, function( $acc, $iterator ) {
			return $acc && $iterator->valid();
		}, TRUE );
	}

}
