<?php

class BleuMetric {

	private $referenceLength;
	private $translationLength;
	private $referenceNGrams;
	private $matchingNGrams;


	public function __construct( Tokenizer $tokenizer ) {
		$this->tokenizer = $tokenizer;
	}

	public function init() {
		$this->referenceLength = 0;
		$this->translationLength = 0;
		$this->referenceNGrams = $this->matchingNGrams = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
	}

	public function addSentence( $reference, $translation ) {
		$referenceNGrams = $this->getNGrams( $reference );
		$translationNGrams = $this->getNGrams( $translation );

		$this->referenceLength += count( $referenceNGrams[1] );
		$this->translationLength += count( $translationNGrams[1] );

		$matchingNGrams = $this->getMatchingNGrams( $referenceNGrams, $translationNGrams );
		$this->addReferenceNGrams( $translationNGrams );
		$this->addMatchingNGrams( $matchingNGrams );

		return $this->getSentenceScore( $referenceNGrams, $translationNGrams, $matchingNGrams );
	}

	public function getSentenceScore( $referenceNGrams, $translationNGrams, $matchingNGrams ) {
		$countOccurences = function( $item ) { return count( $item ); };
		$referenceNGrams = array_map( $countOccurences, $referenceNGrams );
		$translationNGrams = array_map( $countOccurences, $translationNGrams );
		$matchingNGrams = array_map( $countOccurences, $matchingNGrams );

		$geometricAverage = $this->computeGeometricAverage( $matchingNGrams, $translationNGrams, 1 );	
		$brevityPenalty = $this->computeBrevityPenalty( $translationNGrams[1], $referenceNGrams[1] );

		return number_format( $brevityPenalty * exp( $geometricAverage ), 4 );
	}

	private function getNGrams( $sentence ) {
		$tokens = $this->tokenizer->tokenize( $sentence );

		$ngrams = array();
		$ngrams[ 1 ] = $tokens;	
		for( $length = 2; $length <= 4; $length ++ ) {
			array_shift( $tokens );
			$ngrams[ $length ] = $this->joinNGrams( $ngrams[ $length - 1 ], $tokens );
		}

		return $ngrams;
	}

	private function joinNGrams( $ngrams, $tokens ) {
		$result = array();
		for( $i = 0; $i < count( $tokens ); $i++ ) {
			$result[] = "{$ngrams[ $i ]} {$tokens[ $i ]}"; 
		}

		return $result;
	}

	private function getMatchingNGrams( $referenceNGrams, $translationNGrams ) {
		$matchingNGrams = array();
		for( $length = 1; $length <= 4; $length++ ) {
			$matchingNGrams[ $length ] = $this->intersect( $referenceNGrams[ $length ], $translationNGrams[ $length ] );	
		}

		return $matchingNGrams;
	}

	private function intersect( $a, $b ) {
		$aOccurences = $this->countOccurences( $a );
		$bOccurences = $this->countOccurences( $b );

		$intersection = array();
		foreach( $aOccurences as $ngram => $count ) {
			if( !isset( $bOccurences[ $ngram ] ) ) {
				continue;
			}

			for( $i = 0; $i < min( $count, $bOccurences[ $ngram ] ); $i++ ) {
				$intersection[] = $ngram;
			}
		}

		return $intersection;
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

	private function addReferenceNGrams( $translationNGrams ) {
		for( $length = 1; $length <= 4; $length++ ) {
			$this->referenceNGrams[ $length ] += count( $translationNGrams[ $length ] );
		}
	}

	private function addMatchingNGrams( $matchingNGrams ) {
		for( $length = 1; $length <= 4; $length++ ) {
			$this->matchingNGrams[ $length ] += count( $matchingNGrams[ $length ] );
		}
	}

	public function getScore() {
		$geometricAverage = $this->computeGeometricAverage( $this->matchingNGrams, $this->referenceNGrams );	
		$brevityPenalty = $this->computeBrevityPenalty( $this->translationLength, $this->referenceLength );

		return number_format( $brevityPenalty * exp( $geometricAverage ), 4 );
	}

	private function computeGeometricAverage( $matchingNGrams, $referenceNGrams, $default = 0 ) {
		$geometricAverage = 0;

		for( $length = 1; $length <= 4; $length++ ) {
			if( $matchingNGrams[ $length ] == 0 && $default === 0 ) {
				continue;
			}

			$precision = ( $default + $matchingNGrams[ $length ] ) / ( $default + $referenceNGrams[ $length ] );
			$geometricAverage += 1/4 * log( $precision ); 
		}

		return $geometricAverage;
	}


	private function computeBrevityPenalty( $translationLength, $referenceLength ) {
		if( $translationLength <= $referenceLength ) {
			return exp( 1 - $referenceLength / $translationLength );
		} else {
			return 1;
		}
	}

}
