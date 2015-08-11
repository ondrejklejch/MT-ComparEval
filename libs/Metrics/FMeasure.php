<?php

/**
 * F-Measure metric implementation
 */
class FMeasure implements IMetric {

	private $precision;
	private $recall;

	public function __construct( ArithmeticPrecision $precision, ArithmeticRecall $recall ) {
		$this->precision = $precision;
		$this->recall = $recall;
	}

	public function init() {
		$this->precision->init();
		$this->recall->init();
	}

	public function addSentence( $reference, $translation, $meta ) {
		$precision = $this->precision->addSentence( $reference, $translation, $meta );
		$recall = $this->recall->addSentence( $reference, $translation, $meta );

		return $this->computeFMeasure( $precision, $recall );
	}

	public function getScore() {
		$precision = $this->precision->getScore();
		$recall = $this->recall->getScore();

		return $this->computeFMeasure( $precision, $recall );
	}

	private function computeFMeasure( $precision, $recall ) {
		if( $precision == 0 && $recall == 0 ) {
			return 0;
		}

		return number_format( ( 2 * $precision * $recall ) / ( $precision + $recall ), 4 );
	}

}
