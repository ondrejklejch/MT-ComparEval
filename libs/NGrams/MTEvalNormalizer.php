<?php

/**
 * MTEvalNormalizer is used for adding spaces between words
 *
 * @see ftp://jaguar.ncsl.nist.gov/mt/resources/mteval-v13a.pl
 */
class MTEvalNormalizer implements INormalizer {

	public function normalize( $sentence ) {
		$normalized = " $sentence ";

		$normalized = preg_replace( '/&quot;/', '"', $normalized ); # quote to "
		$normalized = preg_replace( '/&amp;/', '&', $normalized ); # ampersand to &
		$normalized = preg_replace( '/&lt;/', '<', $normalized ); # less-than to <
		$normalized = preg_replace( '/&gt;/', '>', $normalized ); # greater-than to >

		$normalized = preg_replace( '/([\{-\~\[-\` -\&\(-\+\:-\@\/])/u', ' $1 ', $normalized );   # tokenize punctuation
		$normalized = preg_replace( '/([^0-9])([\.,])/u', '$1 $2 ', $normalized ); # tokenize period and comma unless preceded by a digit
		$normalized = preg_replace( '/([\.,])([^0-9])/u', ' $1 $2', $normalized ); # tokenize period and comma unless followed by a digit
		$normalized = preg_replace( '/([0-9])(-)/u', '$1 $2 ', $normalized ); # tokenize dash when preceded by a digit
		$normalized = preg_replace( '/\s+/u', ' ', $normalized ); # one space only between words
		$normalized = preg_replace( '/^\s+/u', '', $normalized ); # no leading space
		$normalized = preg_replace( '/\s+$/u', '', $normalized ); # no trailing space

		return $normalized;
	}

}
