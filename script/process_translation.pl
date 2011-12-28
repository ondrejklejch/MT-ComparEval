#!/usr/bin/env perl

use strict;
use warnings;

BEGIN {
    use FindBin qw( $Bin );
    push( @INC, $Bin );
}

use Process;

my $sourcePath = $ARGV[ 0 ];
my $experimentId = $ARGV[ 1 ];
my $taskId = $ARGV[ 2 ];

my $sentencesModel = model( 'TranslationSentences' );
my $ngramsModel = model( 'TranslationNgrams' );
my $position = 0;
open my $file, '<:utf8', $ARGV[ 0 ] or die $!;
while( <$file> ) {
	chomp $_;
	my @tokens = split ' ', $_;

	my $sentence = $sentencesModel->create( {
		experiment_id => $experimentId,
		task_id => $taskId,
		position => $position++,
		text => $_,
		length => $#tokens
	} );	

	for my $length ( 1..4 ) {
		my @stack = @tokens[ 0..( $length-2 ) ];
		
		my $ngramPosition = 0;
		for my $token ( @tokens[ ( $length-1 )..$#tokens ] ) {
			push( @stack, $token );
			my $ngram = $ngramsModel->create( {
				'sentence_id' => $sentence->id,
				'position' => $ngramPosition++,
				'length' => $length,
				'text' => join ' ', @stack,
			} );

			shift( @stack );
		} 
	}

	print "Translation " . $sentence->id . " added to task " . $taskId . "\n";
}

print "Import done\n";
