<?php

/**
 * ConfirmedNGramsPreprocessor adds all confirmed/unconfirmed n-grams to meta informations
 */
class ConfirmedNGramsPreprocessor implements Preprocessor {

	private $confirmedNGramsFinder;

	public function __construct( ConfirmedNGramsFinder $confirmedNGramsFinder ) {
		$this->confirmedNGramsFinder = $confirmedNGramsFinder;
	}


	public function preprocess( $sentence ) {
		$reference = $sentence[ 'meta' ][ 'reference_ngrams' ];
		$translation = $sentence[ 'meta' ][ 'translation_ngrams' ];
		$sentence[ 'meta' ][ 'confirmed_ngrams' ] = $this->confirmedNGramsFinder->getConfirmedNGrams( $reference, $translation );
		$sentence[ 'meta' ][ 'unconfirmed_ngrams' ] = $this->confirmedNGramsFinder->getUnconfirmedNGrams( $reference, $translation );

		return $sentence;
	}

}
