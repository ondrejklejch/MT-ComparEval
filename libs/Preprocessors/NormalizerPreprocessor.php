<?php

/**
 * NormalizerPreprocessor normalizes reference/translation sentences
 */
class NormalizerPreprocessor {

	private $normalizer;

	public function __construct( Normalizer $normalizer ) {
		$this->normalizer = $normalizer;
	}


	public function preprocess( $sentence ) {
		return $this->normalizer->normalize( $sentence );
	}

}
