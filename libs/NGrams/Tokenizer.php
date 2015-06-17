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
		if( !$this->isCaseSensitive ) {
			$sentence = mb_strtolower( $sentence ); 
		}

		return mb_split( '\s+', $sentence );
	}

}
