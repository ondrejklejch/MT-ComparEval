<?php

/**
 * PreprocessorList is used for combinig preprocessors into one preprocessor
 */
class PreprocessorList implements Preprocessor {

	private $preprocessors;

	public function __construct( $preprocessors ) {
		$this->preprocessors = $preprocessors;
	}

	public function preprocess( $sentence ) {
		foreach( $this->preprocessors as $preprocessor ) {
			$sentence = $preprocessor->preprocess( $sentence );
		}

		return $sentence;
	}

}
