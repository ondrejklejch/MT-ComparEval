<?php

/**
 * WordF metric implementation
 */
class WordF implements IMetric {

	private $translationNGrams;
	private $referenceNGrams;
	private $confirmedNGrams;

	public function init() {
		$this->translationNGrams = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
		$this->referenceNGrams = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
		$this->confirmedNGrams = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
	}

	public function addSentence( $reference, $translation, $meta ) {
		$translationNGrams = $meta[ 'translation_ngrams_counts' ];
		$referenceNGrams = $meta[ 'reference_ngrams_counts' ];
		$confirmedNGrams = $meta[ 'confirmed_ngrams_counts' ];

		for( $length = 1; $length <= 4; $length++ ) {
			$this->translationNGrams[ $length ] += $translationNGrams[ $length ];
			$this->referenceNGrams[ $length ] += $referenceNGrams[ $length ];
			$this->confirmedNGrams[ $length ] += $confirmedNGrams[ $length ];
		}

		return $this->computeWordF( $translationNGrams, $referenceNGrams, $confirmedNGrams );
	}

	public function getScore() {
		return $this->computeWordF( $this->translationNGrams, $this->referenceNGrams, $this->confirmedNGrams );
	}

	public function computeWordF( $translationNGrams, $referenceNGrams, $confirmedNGrams ) {
		$wordF = 0;
		for( $length = 1; $length <= 4; $length++ ) {
			if ( $referenceNGrams[ $length ] == 0 ) {
				$recall = 0;
			} else {
				$recall = $confirmedNGrams[ $length ] / $referenceNGrams[ $length ];
			}

			$precision = ( $translationNGrams[ $length ] == 0 ) ? 0 : $confirmedNGrams[ $length ] / $translationNGrams[ $length ];
			$recall = ( $referenceNGrams[ $length ] == 0 ) ? 0 : $confirmedNGrams[ $length ] / $referenceNGrams[ $length ];

			if ( $precision != 0 || $recall != 0 ) {
				$fMeasure = exp( log( $precision ) + log( $recall ) - log( $precision + $recall ) );
				$wordF += 1/4 * $fMeasure;
			}
		}

		return number_format( $wordF, 4 );
	}

}
