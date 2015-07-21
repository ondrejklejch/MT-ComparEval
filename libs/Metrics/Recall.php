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
			$meta[ 'reference_ngrams_counts' ]
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

	private function computeRecall( $confirmedNGrams, $referenceNGrams ) {
		if( $confirmedNGrams[ 1 ] == 0 ) {
			return 0;
		}

		$geometricAverage = 0;
		$smooth = 1;
		for( $length = 1; $length <= 4; $length++ ) {
			if( $referenceNGrams[ $length ] == 0 ) {
				$recall = 1;
			} elseif( $confirmedNGrams[ $length ] == 0 ) {
				$smooth *= 2;
				$recall = 1 / ( $smooth * $referenceNGrams[ $length ] );
			} else {
				$recall = $confirmedNGrams[ $length ] / $referenceNGrams[ $length ];
			}

			$geometricAverage += 1/4 * log( $recall );
		}

		return number_format( exp( $geometricAverage ), 4 );
	}

}
