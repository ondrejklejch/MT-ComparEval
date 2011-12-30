package Bleu;

use strict;
use warnings;

sub compute_bleu {
    my $reference_ngrams_ref = shift;
    my $common_ngrams_ref = shift;
    my $reference_length = shift;
    my $translation_length = shift;

    my $brevity_penalty = get_brevity_penalty( $reference_length, $translation_length );
    my $geometric_average = get_geometric_average( $reference_ngrams_ref, $common_ngrams_ref );  

    return $brevity_penalty * exp( $geometric_average );
}


sub get_brevity_penalty {
    my $reference_length = shift;
    my $translation_length = shift;

    if( $reference_length <= $translation_length ) {
        return exp( 1 - $reference_length / $translation_length );
    } else {
        return 1;
    }
}


sub get_geometric_average {
    my $reference_ngrams_ref = shift;
    my $common_ngrams_ref = shift;

    if( scalar @{ $common_ngrams_ref } < 5 ) {
        return -inf;
    }

    my $average = 0;
    for my $length ( 1..4 ) {
        $average += 1/4 * log( $common_ngrams_ref->[ $length ] / $reference_ngrams_ref->[ $length ] );
    }

    return $average;
}


sub compute_sentence_bleu {
    my $reference_ngrams = shift;
    my $common_ngrams = shift;
    my $translation_length = shift;

    my $average = get_sentence_geometric_average( $reference_ngrams, $common_ngrams );
    my $brevity_penalty = get_brevity_penalty( $reference_ngrams->{ 1 }, $translation_length );

    return sprintf( "%.4f", $brevity_penalty * exp ( $average ) );
}


sub get_sentence_geometric_average {
    my $reference_ngrams_counts = shift;
    my $common_ngrams_counts = shift;

    my $average = 0;
    for my $length ( 1..4 ) {
        my $common_count = $common_ngrams_counts->{ $length };
        my $reference_count = $reference_ngrams_counts->{ $length };
      
        unless( $common_count == 0 ) { 
            $average += 1/4 * log( $common_count / $reference_count ); 
        } else {
            $average += 1/4 * log( 1 / $reference_ngrams_counts->{ 1 } );
        }
    }

    return $average;
}

1;
