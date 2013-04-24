<?php


class NGramsCounterPreprocessor implements Preprocessor {

	public function preprocess( $sentence ) {
		$sentence[ 'meta' ][ 'reference_ngrams_counts' ] = $this->countNGrams( $sentence[ 'meta' ][ 'reference_ngrams' ] );
		$sentence[ 'meta' ][ 'translation_ngrams_counts' ] = $this->countNGrams( $sentence[ 'meta' ][ 'translation_ngrams' ] );
		$sentence[ 'meta' ][ 'confirmed_ngrams_counts' ] = $this->countNGrams( $sentence[ 'meta' ][ 'confirmed_ngrams' ] );

		return $sentence;
	}


	private function countNGrams( $ngrams ) {
		return array_map( function( $item ) { return count( $item ); }, $ngrams );
	}

}
