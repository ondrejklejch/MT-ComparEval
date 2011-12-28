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

my $sentencesSaver = sub {
    my $data = shift;
    $data->{ experiment_id } = $experimentId;
    $data->{ task_id } = $taskId;

    return $sentencesModel->create( $data );	
};

my $ngramsSaver = sub {
    my $data = shift;
    
    return $ngramsModel->create( $data );
};

save( $sourcePath, $sentencesSaver, $ngramsSaver );
print "Import done\n";
