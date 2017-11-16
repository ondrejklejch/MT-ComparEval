<?php


class BrevityPenalty implements IMetric {

	private $referenceLength;
	private $translationLength;

	public function init() {
		$this->referenceLength = 0;
		$this->translationLength = 0;
	}

	public function addSentence( $reference, $translation, $meta ) {
		$referenceLength = $meta[ 'reference_ngrams_counts'][1];
		$translationLength = $meta[ 'translation_ngrams_counts'][1];

		$this->referenceLength += $referenceLength;
		$this->translationLength += $translationLength;

		return $this->computeBrevityPenalty( $referenceLength, $translationLength );
	}

	public function getScore() {
		$referenceLength = $this->referenceLength;
		$translationLength = $this->translationLength;

		return $this->computeBrevityPenalty( $referenceLength, $translationLength );
	}

	private function computeBrevityPenalty( $referenceLength, $translationLength ) {
		if( $translationLength <= $referenceLength ) {
			return exp( 1 - $referenceLength / $translationLength );
		} else {
			return 1;
		}
	}

}
