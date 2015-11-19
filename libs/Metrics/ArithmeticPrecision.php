<?php

/**
 * Modified Precision metric implementation
 */
class ArithmeticPrecision implements IMetric {

	private $translationNGrams;
	private $confirmedNGrams;

	public function init() {
		$this->translationNGrams = $this->confirmedNGrams = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
	}

	public function addSentence( $reference, $translation, $meta ) {
		$this->addTranslationNGrams( $meta[ 'translation_ngrams_counts' ] );
		$this->addConfirmedNgrams( $meta[ 'confirmed_ngrams_counts' ] );

		return $this->computePrecision(
			$meta[ 'confirmed_ngrams_counts' ],
			$meta[ 'translation_ngrams_counts' ]
		);
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
		return $this->computePrecision( $this->confirmedNGrams, $this->translationNGrams );
	}

	private function computePrecision( $confirmedNGrams, $translationNGrams ) {
		if( $confirmedNGrams[ 1 ] == 0 ) {
			return 0;
		}

		$arithmeticAverage = 0;
		$smooth = 1;
		for( $length = 1; $length <= 4; $length++ ) {
			if( $translationNGrams[ $length ] == 0 ) {
				$precision = 1;
			} else {
				$precision = $confirmedNGrams[ $length ] / $translationNGrams[ $length ];
			}

			$arithmeticAverage += 1/4 * $precision;
		}

		return number_format( $arithmeticAverage * 100, 2 );
	}

}
