#!/usr/bin/env perl

use strict;
use warnings;
use FindBin qw( $Bin );
use Path::Class;
use lib dir( $Bin, '..', 'lib' )->stringify;
use MTComparEval::Model::DBIC;
use Config::JFDI;
use Bleu;


sub model {
    my $tableName = shift;

    my $filename = file( $Bin, '..', 'mtcompareval.conf' );
    my $home = file( $Bin, '..' );
    my $config = Config::JFDI->new( {
        path => $filename->stringify,
        path_to => $home->stringify
    } );
    my $dsn = $config->get->{ 'Model::DBIC' }->{ connect_info };
    my $model = MTComparEval::Model::DBIC->new();
    my $schema = $model->connect( $dsn ) or die $!;

    return $schema->resultset( $tableName );
}


sub save {
    my $filename = shift;
    my $sentencesSaver = shift;
    my $ngramsSaver = shift;

    my $position = 0;
    open my $file, '<:utf8', $filename or die $!;
    while( <$file> ) {
        chomp $_;
        my @tokens = split ' ', $_;

        my $sentence = $sentencesSaver->( {
            position => $position++,
            text => $_,
            length => $#tokens + 1
        } );    

        
        if( $ngramsSaver ) {
            saveNGrams( $sentence->id, \@tokens, $ngramsSaver );
        }
    
        print "Translation " . $sentence->id . " added\n";
    }
}


sub saveNGrams {
    my $sentenceId = shift;
    my $tokens_ref = shift;
    my $saver = shift;

    my @tokens = @{ $tokens_ref };
    for my $length ( 1..4 ) {
        my @stack = @tokens[ 0..( $length-2 ) ];
        
        my $ngramPosition = 0;
        for my $token ( @tokens[ ( $length-1 )..$#tokens ] ) {
            push( @stack, $token );
            my $ngram = $saver->( {
                'sentence_id' => $sentenceId,
                'position' => $ngramPosition++,
                'length' => $length,
                'text' => join ' ', @stack,
            } );

            shift( @stack );
        } 
    }
}


sub get_bleu_for_task {
    my $task_id = shift;

    my $tasks_model = model( 'Tasks' );
    my $task = $tasks_model->find( { id => $task_id } );
    my $experiment_id = $task->get_column( 'experiment_id' );
    
    my $translation_length = $tasks_model->getTranslationLength( $task_id );
    
    my $common_ngrams_model = model( 'CommonNGrams' );
    my $common_ngrams = $common_ngrams_model->getCommonNGramsCountByLength( $experiment_id, $task_id );
    
    my $experiments_model = model( 'Experiments' );
    my $reference_ngrams = $experiments_model->getReferenceNGramsCountByLength( $experiment_id );
    my $reference_length = $experiments_model->getReferenceTranslationLength( $experiment_id );
    
    my $bleu = Bleu::compute_bleu( $reference_ngrams, $common_ngrams, $reference_length, $translation_length );

    return sprintf( "%.4f", $bleu );
}



sub compute_and_set_bleu_for_sentences_of_task {
    my $task_id = shift;
    my $saver = shift;

    my $tasks_model = model( 'Tasks' );
    my $task = $tasks_model->find( { id => $task_id } );
    my $experiment_id = $task->get_column( 'experiment_id' );

    my @common_ngrams = model( 'CommonNGrams' )->searchCommonNGramsCountByLengthAndPosition( $experiment_id, $task_id )->all;
    my @reference_ngrams = model( 'Experiments' )->getReferenceNGramsCountByLengthAndPosition( $experiment_id )->all;
    my @translations_lengths = model( 'Tasks' )->getTranslationsLengthByPosition( $task_id )->all;

    my $common_ngrams_counts = get_ngrams_counts( \@common_ngrams );
    my $reference_ngrams_counts = get_ngrams_counts( \@reference_ngrams );
    my $lengths = get_sentences_lengths( \@translations_lengths );

    for my $position ( keys %{ $common_ngrams_counts } ) {
        my $bleu = Bleu::compute_sentence_bleu(
            $reference_ngrams_counts->{ $position },
            $common_ngrams_counts->{ $position },
            $lengths->{ $position }
        );
    
        $saver->( $position, $bleu );
    }
}


sub get_ngrams_counts {
    my $ngrams_on_positions = shift;

    my $counts = {};
    for my $ngrams ( @{ $ngrams_on_positions } ) {
        my $position = $ngrams->get_column( 'position' ); 
        unless( exists $counts->{ $position } ) {
            for my $length ( 1..4 ) {
    	        $counts->{ $position }->{ $length } = 0;
            }
        }
    
        $counts->{ $position }->{ $ngrams->get_column( 'length' ) } = $ngrams->get_column( 'count' );
    }

    return $counts;
}


sub get_sentences_lengths {
    my $translations = shift;

    my $lengths = {};
    for my $translation ( @{ $translations } ) {
        $lengths->{ $translation->get_column( 'position' ) } = $translation->get_column( 'length' );
    }

    return $lengths;
}

1;