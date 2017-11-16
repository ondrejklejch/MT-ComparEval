<?php

/**
 * Bleu metric implementation
 */
class Bleu implements IMetric {

	private $precision;
	private $brevityPenalty;

	public function __construct( GeometricPrecision $precision, BrevityPenalty $brevityPenalty ) {
		$this->precision = $precision;
		$this->brevityPenalty = $brevityPenalty;
	}

	public function init() {
		$this->precision->init();
		$this->brevityPenalty->init();
	}

	public function addSentence( $reference, $translation, $meta ) {
		$precision = $this->precision->addSentence( $reference, $translation, $meta );
		$brevityPenalty = $this->brevityPenalty->addSentence( $reference, $translation, $meta );

		return $this->computeBleu( $precision, $brevityPenalty );
	}

	public function getScore() {
		$precision = $this->precision->getScore();
		$brevityPenalty = $this->brevityPenalty->getScore();

		return $this->computeBleu( $precision, $brevityPenalty );
	}

	public function computeBleu( $precision, $brevityPenalty ) {
		return number_format( $brevityPenalty * $precision, 2 );
	}

}
