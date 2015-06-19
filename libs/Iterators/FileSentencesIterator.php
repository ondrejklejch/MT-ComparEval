<?php

/**
 * FileSentencesIterator is used for lazy iterating over lines in given file
 */
class FileSentencesIterator extends IteratorIterator implements ISentencesIterator {

	private $count = NULL;

	public function __construct( $path ) {
		if( !file_exists( $path ) || !is_readable( $path ) ) {
			throw new InvalidSentencesResourceException();
		}

		$sentences = preg_split( '/\n/u', trim( file_get_contents( $path ) ) );
		$this->count = count( $sentences );

		return parent::__construct( new ArrayIterator( $sentences ) );
	}

	public function count() {
		return $this->count;
	}

}
