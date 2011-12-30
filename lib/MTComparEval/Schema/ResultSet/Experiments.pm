package MTComparEval::Schema::ResultSet::Experiments;

use strict;
use warnings;

use base 'DBIx::Class::ResultSet';

sub getReferenceNGramsCountByLength {
    my $self = shift;
    my $experimentId = shift;

    my $experiment = $self->find( { id => $experimentId } );
    my $referenceSentences = $experiment->search_related( 'reference_sentences' );
    my $referenceNGrams = $referenceSentences->search_related( 'reference_ngrams' );

    my @referenceNGrams = $referenceNGrams->search( undef, {
        select => [ 'reference_ngrams.length', { count => '1', -as => 'count' } ],
        group_by => [ 'reference_ngrams.length' ],
    } ); 

    my @ngrams = [];
    for my $ngramCount ( @referenceNGrams ) {
        $ngrams[ $ngramCount->get_column( 'length' ) ] = $ngramCount->get_column( 'count' );
    }

    return \@ngrams;
}


sub getReferenceNGramsCountByLengthAndPosition {
    my $self = shift;
    my $experimentId = shift;

    my $experiment = $self->find( { id => $experimentId } );
    my $referenceSentences = $experiment->search_related( 'reference_sentences' );
    my $referenceNGrams = $referenceSentences->search_related( 'reference_ngrams' );

    return $referenceNGrams->search( undef, {
        select => [ 'me.position', 'reference_ngrams.length', { count => '1', -as => 'count' } ],
        as => [ 'position', 'length', 'count' ],
        group_by => [ 'me.position', 'reference_ngrams.length' ],
    } ); 
}


sub getReferenceTranslationLength {
    my $self = shift;
    my $experimentId = shift;

    my $experiment = $self->find( { id => $experimentId } );
    my $referenceSentences = $experiment->search_related( 'reference_sentences' );

    return $referenceSentences->search( undef, {
        select => [ { sum => 'length', -as => 'length' } ],
        as => [ 'length' ]
    } )->first->get_column( 'length' ) || 0;
}

1;
