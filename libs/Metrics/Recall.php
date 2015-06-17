<?php

/**
 * Modified Recall metric implementation
 *
 * For sentence level metrics smoothing defined in article about BLEU_s is used
 */
class Recall implements IMetric {

	private $referenceNGrams;
	private $confirmedNGrams;

	public function init() {
		$this->referenceNGrams = $this->confirmedNGrams = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
	}

	public function addSentence( $reference, $reference, $meta ) {
		$this->addReferenceNGrams( $meta[ 'reference_ngrams_counts' ] );
		$this->addConfirmedNgrams( $meta[ 'confirmed_ngrams_counts' ] );

		return $this->computeRecall(
			$meta[ 'confirmed_ngrams_counts' ],
			$meta[ 'reference_ngrams_counts' ],
			1 
		);
	}

	private function addReferenceNGrams( $referenceNGrams ) {
		for( $length = 1; $length <= 4; $length++ ) {
			$this->referenceNGrams[ $length ] += $referenceNGrams[ $length ];
		}
	}

	private function addConfirmedNgrams( $confirmedNGrams ) {
		for( $length = 1; $length <= 4; $length++ ) {
			$this->confirmedNGrams[ $length ] += $confirmedNGrams[ $length ];
		}
	}

	public function getScore() {
		return $this->computeRecall( $this->confirmedNGrams, $this->referenceNGrams );
	}

	private function computeRecall( $confirmedNGrams, $referenceNGrams, $default = 0 ) {
		if( $confirmedNGrams[ 1 ] == 0 ) {
			return 0;
		}

		$geometricAverage = 0;
		for( $length = 1; $length <= 4; $length++ ) {
			if( $confirmedNGrams[ $length ] == 0 && $default == 0 ) {
				continue;
			}

			if( $length > 1 ) {
				$precision = ( $default + $confirmedNGrams[ $length ] ) / ( $default + $referenceNGrams[ $length ] );
			} else {
				$precision = $confirmedNGrams[ $length ] / $referenceNGrams[ $length ];
			}

			$geometricAverage += 1/4 * log( $precision ); 
		}

		return number_format( exp( $geometricAverage ), 4 );
	}

}
