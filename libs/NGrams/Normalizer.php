<?php

/**
 * Normalizer is used for adding spaces between words
 *
 * @see ftp://jaguar.ncsl.nist.gov/mt/resources/mteval-v13a.pl
 */
class Normalizer {

	public function normalize( $sentence ) {
		$normalized = " $sentence ";
		$normalized = preg_replace( '/([\{-\~\[-\` -\&\(-\+\:-\@\/])/', ' $1 ', $normalized );	# tokenize punctuation
		$normalized = preg_replace( '/([^0-9])([\.,])/', '$1 $2 ', $normalized ); # tokenize period and comma unless preceded by a digit
		$normalized = preg_replace( '/([\.,])([^0-9])/', ' $1 $2', $normalized ); # tokenize period and comma unless followed by a digit
		$normalized = preg_replace( '/([0-9])(-)/', '$1 $2 ', $normalized ); # tokenize dash when preceded by a digit
		$normalized = preg_replace( '/\s+/', ' ', $normalized ); # one space only between words
		$normalized = preg_replace( '/^\s+/',  '', $normalized );  # no leading space
		$normalized = preg_replace( '/\s+$/' , '', $normalized );  # no trailing space

		return $normalized;
	}

}
