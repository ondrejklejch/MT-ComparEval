<?php

/**
 * BootstrapSampler is used for generating samples for Bootstrap Sampling method
 *
 * This implementation can be also used for Paired Bootstrap Sampling, because
 * it fixes random seed - so everytime the samples will be using same sentences
 */
class BootstrapSampler {

	private $repeats = 0;

	public function __construct( $repeats ) {
		$this->repeats = $repeats;
	}


	public function generateSamples( $metric, $sentences ) {
		mt_srand( 0 );
		$count = count( $sentences );

		$samples = array();
		for( $i = 0; $i < $this->repeats; $i++ ) {
			$metric->init();

			for( $j = 0; $j < $count; $j++ ) {
				$rand = mt_rand( 0, $count - 1 );

				$metric->addSentence( $sentences[ $rand ][ 'experiment' ][ 'reference' ], $sentences[ $rand ][ 'translation' ], $sentences[ $rand ][ 'meta' ] );
			}

			$samples[] = $metric->getScore();
		}

		return $samples;
	}

}
