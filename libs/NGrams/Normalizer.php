<?php

/**
 * Normalizer is used for adding spaces between words
 *
 * @see ftp://jaguar.ncsl.nist.gov/mt/resources/mteval-v13a.pl
 */
class Normalizer {

	public function normalize( $sentence ) {
		$normalized = " $sentence ";
		$normalized = preg_replace( '/(\P{N})(\p{P})/u', '$1 $2 ', $normalized ); # tokenize punctuation unless preceded by a digit
		$normalized = preg_replace( '/(\p{P})(\P{N})/u', ' $1 $2', $normalized ); # tokenize punctuation unless followed by a digit

		$normalized = preg_replace( '/(\p{S})/u', ' $1 ', $normalized ); # tokenize symbols
		$normalized = preg_replace( '/\p{Z}+/u', ' ', $normalized ); # one space only between words
		$normalized = preg_replace( '/^\p{Z}+/u',  '', $normalized );  # no leading space
		$normalized = preg_replace( '/\p{Z}+$/u' , '', $normalized );  # no trailing space

		return $normalized;
	}

}
