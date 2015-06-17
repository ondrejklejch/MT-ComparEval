<?php

/**
 * NGramsPreprocessor adds all reference/translation n-grams to meta informations
 */
class NGramsPreprocessor implements Preprocessor {

	private $ngramizer;

	public function __construct( NGramizer $ngramizer ) {
		$this->ngramizer = $ngramizer;
	}


	public function preprocess( $sentence ) {
		$reference = $sentence[ 'meta' ][ 'reference' ];
		$translation = $sentence[ 'meta' ][ 'translation' ];

		$sentence[ 'meta' ][ 'reference_ngrams' ] = $this->ngramizer->getNGrams( $reference );
		$sentence[ 'meta' ][ 'translation_ngrams' ] = $this->ngramizer->getNGrams( $translation );

		return $sentence;
	}

}
