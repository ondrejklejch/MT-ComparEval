<?php

/**
 * InitialPreprocessor init preprocessing by creating initial meta informations
 */
class InitialPreprocessor implements Preprocessor {

	public function preprocess( $sentence ) {
		$isCaseSensitive = $sentence[ 'is_case_sensitive' ];

		$sentence[ 'meta' ] = array();

		if( $isCaseSensitive ) {
			$sentence[ 'meta' ][ 'reference' ] = $sentence[ 'experiment' ][ 'reference' ];
			$sentence[ 'meta' ][ 'translation' ] = $sentence[ 'translation' ];
		} else {
			$sentence[ 'meta' ][ 'reference' ] = mb_strtolower( $sentence[ 'experiment' ][ 'reference' ] );
			$sentence[ 'meta' ][ 'translation' ] = mb_strtolower( $sentence[ 'translation' ] );
		}

		return $sentence;
	}

}
