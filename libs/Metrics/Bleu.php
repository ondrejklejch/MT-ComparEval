<?php

/**
 * Bleu metric implementation
 */
class Bleu implements IMetric {

	private $precision;
	private $referenceLength;
	private $translationLength;

	public function __construct( GeometricPrecision $precision ) {
		$this->precision = $precision;
	}

	public function init() {
		$this->precision->init();
		$this->referenceLength = 0;
		$this->translationLength = 0;
	}

	public function addSentence( $reference, $translation, $meta ) {
		$precision = $this->precision->addSentence( $reference, $translation, $meta );
		$referenceLength = $meta[ 'reference_ngrams_counts'][1];
		$translationLength = $meta[ 'translation_ngrams_counts'][1];

		$this->referenceLength += $referenceLength;
		$this->translationLength += $translationLength;

		return $this->computeBleu( $precision, $referenceLength, $translationLength );
	}

	public function getScore() {
		$precision = $this->precision->getScore();
		$referenceLength = $this->referenceLength;
		$translationLength = $this->translationLength;

		return $this->computeBleu( $precision, $referenceLength, $translationLength );
	}

	public function computeBleu( $precision, $referenceLength, $translationLength ) {
		$brevityPenalty = $this->computeBrevityPenalty( $referenceLength, $translationLength );

		return number_format( $brevityPenalty * $precision, 2 );
	}

	private function computeBrevityPenalty( $referenceLength, $translationLength ) {
		if( $translationLength <= $referenceLength ) {
			return exp( 1 - $referenceLength / $translationLength );
		} else {
			return 1;
		}
	}











}
