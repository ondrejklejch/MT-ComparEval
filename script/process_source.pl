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

my $sentencesModel = model( 'SourceSentences' );
my $sentencesSaver = sub {
    my $data = shift;
    $data->{ experiment_id } = $experimentId;
    delete( $data->{ length } );    

    return $sentencesModel->create( $data );	
};

save( $sourcePath, $sentencesSaver );
print "Import done\n";
