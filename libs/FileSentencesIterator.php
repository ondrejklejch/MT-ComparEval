<?php

class FileSentencesIterator implements ISentencesIterator {

	const INVALID = "###INVALID_LINE###";

	private $handle;
	private $line = "";
	private $position = 0;
	private $count = NULL;	

	public function __construct( $path ) {
		if( !file_exists( $path ) || !is_readable( $path ) ) {
			throw new InvalidSentencesResourceException();
		}

		$this->handle = fopen( $path, 'r' );
	}

	public function __destruct() {
		fclose( $this->handle );
	}

	public function current() {
		return $this->line;
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		if( !feof( $this->handle ) ) {
			$this->line = trim( fgets( $this->handle ) );
			$this->position++;
		} else {
			$this->line = self::INVALID;
		}
	}

	public function rewind() {
		rewind( $this->handle );
		$this->position = 0;
		
		$this->next();
	}

	public function valid() {
		return $this->line !== self::INVALID;
	}

	public function count() {
		if( $this->count === NULL ) {
			$this->count = iterator_count( $this );
		}

		return $this->count; 
	}

}
