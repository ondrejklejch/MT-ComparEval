#!/usr/bin/perl

use strict;
use warnings;
use Tokenizer;
use Normalizer;


sub print_sentence_row {
	my( $sentencePosition, $length, $sentence ) = @_;
	
	print 'SELECT ', $sentencePosition,' AS "position",',$length,' AS "length", "',$sentence,'" AS "text"',"\n";
}

open my $file, '<', $ARGV[0];

my $sentencePosition = 0;
while( my $sentence = <$file> ) {
	$sentence = Normalizer::normalize( $sentence );
	my @tokens = Tokenizer::tokenize( $sentence );
	print_sentence_row( $sentencePosition, $#tokens, $sentence );
	
	$sentencePosition++;
}
