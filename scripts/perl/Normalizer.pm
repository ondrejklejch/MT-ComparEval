#!/usr/bin/perl

package Normalizer;

use strict;
use warnings;


sub normalize {
	my( $norm_text ) = @_;

	# language-dependent part (assuming Western languages):
	$norm_text = " $norm_text ";
	$norm_text =~ s/([\{-\~\[-\` -\&\(-\+\:-\@\/])/ $1 /g;	# tokenize punctuation
	$norm_text =~ s/([^0-9])([\.,])/$1 $2 /g; # tokenize period and comma unless preceded by a digit
	$norm_text =~ s/([\.,])([^0-9])/ $1 $2/g; # tokenize period and comma unless followed by a digit
	$norm_text =~ s/([0-9])(-)/$1 $2 /g; # tokenize dash when preceded by a digit
	$norm_text =~ s/\s+/ /g; # one space only between words
	$norm_text =~ s/^\s+//;  # no leading space
	$norm_text =~ s/\s+$//;  # no trailing space
	$norm_text =~ s/'/&#39;/g;
	$norm_text =~ s/"/&quot;/g;

	return $norm_text;
}

1;
