#!/usr/bin/env perl

use strict;
use warnings;
use FindBin qw( $Bin );
use Path::Class;
use lib dir( $Bin, '..', 'lib' )->stringify;
use MTComparEval::Model::DBIC;
use Config::JFDI;

sub model {
    my $tableName = shift;

    my $filename = file( $Bin, '..', 'mtcompareval.conf' );
    my $home = file( $Bin, '..' );
    my $config = Config::JFDI->new( {
        path => $filename->stringify,
        path_to => $home->stringify
    } );
    my $dsn = $config->get->{ 'Model::MTComparEval' }->{ connect_info };
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
            length => $#tokens
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

1;
