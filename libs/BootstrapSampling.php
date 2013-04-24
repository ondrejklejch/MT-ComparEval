<?php


class BootstrapSampler {

	private $repeats = 0;

	private $testSetSize = 0;

	public function __construct( $repeats, $testSetSize ) {
		$this->repeats = $repeats;
		$this->testSetSize = $testSetSize;
	}


	public function generateSamples( $metric, $sentences ) {
		echo "generating samples\n";
		srand( 0 );
		$count = count( $sentences );

		$samples = array();
		for( $i = 0; $i < $this->repeats; $i++ ) {
			$metric->init();

			for( $j = 0; $j < $this->testSetSize; $j++ ) {
				$rand = rand( 0, $count );

				$metric->addSentence( $sentences[ $rand ][ 'experiment' ][ 'reference' ], $sentences[ $rand ][ 'translation' ] );
			}

			$samples[] = $metric->getScore();
		}
		echo "samples generated\n";

		return $samples;
	}

}
