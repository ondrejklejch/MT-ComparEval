<?php

class NGramizer {

	private $tokenizer;

	public function __construct( Tokenizer $tokenizer ) {
		$this->tokenizer = $tokenizer;
	}


	public function getNGrams( $sentence ) {
		$tokens = $this->tokenizer->tokenize( $sentence );

		$ngrams = array();
		$ngrams[ 1 ] = $tokens;	
		for( $length = 2; $length <= 4; $length ++ ) {
			array_shift( $tokens );
			$ngrams[ $length ] = $this->joinNGrams( $ngrams[ $length - 1 ], $tokens );
		}

		return $ngrams;
	}

	private function joinNGrams( $ngrams, $tokens ) {
		$result = array();
		for( $i = 0; $i < count( $tokens ); $i++ ) {
			$result[] = "{$ngrams[ $i ]} {$tokens[ $i ]}"; 
		}

		return $result;
	}

}
