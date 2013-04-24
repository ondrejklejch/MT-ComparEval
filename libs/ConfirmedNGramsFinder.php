<?php

class ConfirmedNGramsFinder {

	public function getConfirmedNGrams( $referenceNGrams, $translationNGrams ) {
		$confirmedNGrams = array();
		for( $length = 1; $length <= 4; $length++ ) {
			$confirmedNGrams[ $length ] = $this->intersect( $referenceNGrams[ $length ], $translationNGrams[ $length ] );	
		}

		return $confirmedNGrams;
	}

	public function getUnconfirmedNGrams( $referenceNGrams, $translationNGrams ) {
		$unconfirmedNGrams = array();
		for( $length = 1; $length <= 4; $length++ ) {
			$unconfirmedNGrams[ $length ] = $this->diff( $translationNGrams[ $length ], $referenceNGrams[ $length ] );
		}

		return $unconfirmedNGrams;
	}

	private function intersect( $a, $b ) {
		return $this->setOperation( $a, $b, function( $a, $b ) { return min( $a, $b ); } );
	}

	private function diff( $a, $b ) {
		return $this->setOperation( $a, $b, function( $a, $b ) { return $a - $b; } );
	}

	private function setOperation( $a, $b, $occurencesCounter ) {
		$aOccurences = $this->countOccurences( $a );
		$bOccurences = $this->countOccurences( $b );

		$newSet = array();
		foreach( $aOccurences as $ngram => $count ) {
			if( !isset( $bOccurences[ $ngram ] ) ) {
				$bOccurences[ $ngram ] = 0;
			}

			for( $i = 0; $i < $occurencesCounter( $count, $bOccurences[ $ngram ] ); $i++ ) {
				$newSet[] = $ngram;
			}
		}

		return $newSet;
	}

	private function countOccurences( $array ) {
		$occurences = array();
		foreach( $array as $value ) {
			if( !isset( $occurences[ $value ] ) ) {
				$occurences[ $value ] = 0;
			}

			$occurences[ $value ]++;
		}

		return $occurences;
	}

}
