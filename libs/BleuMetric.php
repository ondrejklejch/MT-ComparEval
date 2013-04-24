<?php

class BleuMetric {

	private $ngramizer;
	private $referenceLength;
	private $translationLength;
	private $translationNGrams;
	private $confirmedNGrams;


	public function __construct( NGramizer $ngramizer ) {
		$this->ngramizer = $ngramizer;
	}

	public function init() {
		$this->referenceLength = 0;
		$this->translationLength = 0;
		$this->translationNGrams = $this->confirmedNGrams = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
	}

	public function addSentence( $reference, $translation, $meta ) {
		$this->referenceLength += $meta[ 'reference_ngrams_counts' ][1];
		$this->translationLength += $meta[ 'translation_ngrams_counts' ][1];

		$this->addTranslationNGrams( $meta[ 'translation_ngrams_counts' ] );
		$this->addConfirmedNgrams( $meta[ 'confirmed_ngrams_counts' ] );

		return $this->getSentenceScore(
			$meta[ 'reference_ngrams_counts' ],
			$meta[ 'translation_ngrams_counts' ],
			$meta[ 'confirmed_ngrams_counts' ]
		);
	}

	public function getSentenceScore( $referenceNGramsCounts, $translationNGramsCounts, $confirmedNGramsCounts ) {
		$geometricAverage = $this->computeGeometricAverage( $confirmedNGramsCounts, $translationNGramsCounts, 1 );	
		$brevityPenalty = $this->computeBrevityPenalty( $translationNGramsCounts[1], $referenceNGramsCounts[1] );

		return number_format( $brevityPenalty * exp( $geometricAverage ), 4 );
	}

	private function addTranslationNGrams( $translationNGrams ) {
		for( $length = 1; $length <= 4; $length++ ) {
			$this->translationNGrams[ $length ] += $translationNGrams[ $length ];
		}
	}

	private function addConfirmedNgrams( $confirmedNGrams ) {
		for( $length = 1; $length <= 4; $length++ ) {
			$this->confirmedNGrams[ $length ] += $confirmedNGrams[ $length ];
		}
	}

	public function getScore() {
		$geometricAverage = $this->computeGeometricAverage( $this->confirmedNGrams, $this->translationNGrams );	
		$brevityPenalty = $this->computeBrevityPenalty( $this->translationLength, $this->referenceLength );

		return number_format( $brevityPenalty * exp( $geometricAverage ), 4 );
	}

	private function computeGeometricAverage( $confirmedNGrams, $translationNGrams, $default = 0 ) {
		$geometricAverage = 0;

		for( $length = 1; $length <= 4; $length++ ) {
			if( $confirmedNGrams[ $length ] == 0 && $default === 0 ) {
				continue;
			}

			$precision = ( $default + $confirmedNGrams[ $length ] ) / ( $default + $translationNGrams[ $length ] );
			$geometricAverage += 1/4 * log( $precision ); 
		}

		return $geometricAverage;
	}


	private function computeBrevityPenalty( $translationLength, $referenceLength ) {
		if( $translationLength <= $referenceLength ) {
			return exp( 1 - $referenceLength / $translationLength );
		} else {
			return 1;
		}
	}

}
