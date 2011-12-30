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

    if( $translation_length <= $reference_length ) {
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

1;
