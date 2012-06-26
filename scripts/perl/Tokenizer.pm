#!/usr/bin/perl

package Tokenizer;

use strict;
use warnings;

sub tokenize {
	my( $norm_text ) = @_;

	return split( /\s+/, $norm_text );
}

1;
