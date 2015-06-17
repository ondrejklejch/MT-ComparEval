<?php

/**
 * Modified Precision metric implementation
 *
 * For sentence level metrics smoothing defined in article about BLEU_s is used
 */
class Precision implements IMetric {

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
			$meta[ 'translation_ngrams_counts' ],
			1 
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

	private function computePrecision( $confirmedNGrams, $translationNGrams, $default = 0 ) {
		if( $confirmedNGrams[ 1 ] == 0 ) {
			return 0;
		}

		$geometricAverage = 0;
		for( $length = 1; $length <= 4; $length++ ) {
			if( $confirmedNGrams[ $length ] == 0 && $default == 0 ) {
				continue;
			}

			if( $length > 1 ) {
				$precision = ( $default + $confirmedNGrams[ $length ] ) / ( $default + $translationNGrams[ $length ] );
			} else {
				$precision = $confirmedNGrams[ $length ] / $translationNGrams[ $length ];
			}

			$geometricAverage += 1/4 * log( $precision ); 
		}

		return number_format( exp( $geometricAverage ), 4 );
	}

}
