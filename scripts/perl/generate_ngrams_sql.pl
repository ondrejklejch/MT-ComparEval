#!/usr/bin/perl

use strict;
use warnings;
use Tokenizer;
use Normalizer;


sub print_ngram_row {
	my( $sentencePosition, $wordPosition, $length, $ngram, $nth ) = @_;
	
	print 'SELECT "', $ngram, '" AS "text", ', $sentencePosition, ' AS "sentence_id",', $length,' AS "length", ', $wordPosition,' AS "position", ', $nth, ' AS "nth"',"\n";
}


sub create_hash {
	my( $sentencePosition, $wordPosition, $length, $text ) = @_;

	return { 
		'sentencePosition' => $sentencePosition,
		'wordPosition' => $wordPosition,
		'length' => $length,
		'text' => $text,
	};

}

open my $file, '<', $ARGV[0];

my $sentencePosition = 0;
while( my $sentence = <$file> ) {
	my @tokens = Tokenizer::tokenize( Normalizer::normalize( $sentence ) );
	my @ngrams;

	my $wordPosition = 0;
	for (; @tokens; shift @tokens) {
		my ($ngram, $word);
		
		for (my $length=1; $length<=4 and defined($word=$tokens[$length-1]); $length++) {
			$ngram .= defined $ngram ? " $word" : $word;
			
			push( @ngrams, create_hash( $sentencePosition, $wordPosition, $length, $ngram ) );
		}
		
		$wordPosition++;		
	}

	my $last = {};
	my $lastCount = 0;
	foreach my $ngram ( sort { $a->{ 'text' } cmp $b->{ 'text' } } @ngrams ) {
		if( $last ne $ngram->{ 'text' } ) {
			$last = $ngram->{ 'text' };
			$lastCount = 0;
		}

		print_ngram_row( $ngram->{ 'sentencePosition' }, $ngram->{ 'wordPosition' }, $ngram->{ 'length' }, $ngram->{ 'text' }, ++$lastCount );
	}
	
	$sentencePosition++;
}
