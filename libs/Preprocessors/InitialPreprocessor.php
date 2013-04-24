<?php


class InitialPreprocessor implements Preprocessor {

	public function preprocess( $sentence ) {
		$sentence[ 'meta' ] = array();
		$sentence[ 'meta' ][ 'reference' ] = $sentence[ 'experiment' ][ 'reference' ];
		$sentence[ 'meta' ][ 'translation' ] = $sentence[ 'translation' ];

		return $sentence;
	}

}
