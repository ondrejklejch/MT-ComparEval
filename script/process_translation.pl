#!/usr/bin/env perl

use strict;
use warnings;

BEGIN {
    use FindBin qw( $Bin );
    push( @INC, $Bin );
}

use Process;

my $source_path = $ARGV[ 0 ];
my $experiment_id = $ARGV[ 1 ];
my $task_id = $ARGV[ 2 ];

my $sentences_model = model( 'TranslationSentences' );
my $sentences_saver = sub {
    my $data = shift;
    $data->{ experiment_id } = $experiment_id;
    $data->{ task_id } = $task_id;

    return $sentences_model->create( $data );	
};


my $ngrams_model = model( 'TranslationNgrams' );
my $ngrams_saver = sub {
    my $data = shift;
    
    return $ngrams_model->create( $data );
};

save( $source_path, $sentences_saver, $ngrams_saver );

my $bleu = get_bleu_for_task( $task_id );
my $task = model( 'Tasks' )->find( { id => $task_id } );
$task->set_column( 'bleu', $bleu );
$task->set_column( 'state', 1 );
$task->update();

my $sentence_blue_saver = sub {
    my $position = shift;
    my $bleu = shift;

    my $sentence = $sentences_model->find( { task_id => $task_id, position => $position } );
    $sentence->set_column( 'diff_bleu', $bleu );
    $sentence->update();
};

compute_and_set_bleu_for_sentences_of_task( $task_id, $sentence_blue_saver ); 

print "Import done\n";
