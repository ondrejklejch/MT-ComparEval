#!/usr/bin/env perl

use strict;
use warnings;
use NGram;

my $ngram = new NGram();

open my $reference_file, '<', $ARGV[0];
open my $machine_file, '<', $ARGV[1];

my @reference_translations;
my @machine_translations;

while( my $reference = <$reference_file> ) {
	chomp( $reference );
	push( @reference_translations, $reference ); 
} 

while( my $machine = <$machine_file> ) {
	chomp( $machine );
	push( @machine_translations, $machine );
}

while( $#reference_translations >= 0 ) {
	$ngram->add_sentence(
		pop( @reference_translations ),
		pop( @machine_translations )
	);
}

print $ngram->get_bleu();

close( $reference_file );
close( $machine_file );
