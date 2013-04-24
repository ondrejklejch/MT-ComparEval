<?php


class BootstrapSampler {

	private $repeats = 0;

	private $testSetSize = 0;

	public function __construct( $repeats, $testSetSize ) {
		$this->repeats = $repeats;
		$this->testSetSize = $testSetSize;
	}


	public function generateSamples( $metric, $sentences ) {
		mt_srand( 0 );
		$count = count( $sentences );

		$samples = array();
		for( $i = 0; $i < $this->repeats; $i++ ) {
			$metric->init();

			for( $j = 0; $j < $this->testSetSize; $j++ ) {
				$rand = mt_rand( 0, $count - 1 );

				$metric->addSentence( $sentences[ $rand ][ 'experiment' ][ 'reference' ], $sentences[ $rand ][ 'translation' ], $sentences[ $rand ][ 'meta' ] );
			}

			$samples[] = $metric->getScore();
		}

		return $samples;
	}

}
