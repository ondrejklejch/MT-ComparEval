<?php

/**
 * Tokenizer splits sentence into tokens
 */
class Tokenizer {

	private $isCaseSensitive;

	public function __construct( $isCaseSensitive = true ) {
		$this->isCaseSensitive = $isCaseSensitive;
	}

	public function tokenize( $sentence ) {
		mb_internal_encoding("UTF-8");
		if( !$this->isCaseSensitive ) {
			$sentence = mb_strtolower( $sentence );
		}

		return preg_split( '/\s+/u', $sentence );
	}

}
