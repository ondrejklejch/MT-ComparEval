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

my $model = model( 'SourceSentences' );
my $position = 0;
open my $file, '<:utf8', $ARGV[ 0 ] or die $!;
while( <$file> ) {
    chomp;

    my $sentence = $model->create( { 
        experiment_id => $experimentId,
        text => $_,
        position => $position++,
    } );

    print "Sentence " . $sentence->id . " added to experiment " . $experimentId . "\n";
}

print "Import done\n";
