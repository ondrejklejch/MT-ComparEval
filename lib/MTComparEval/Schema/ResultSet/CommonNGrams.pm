package MTComparEval::Schema::ResultSet::CommonNGrams;

use strict;
use warnings;

use base 'DBIx::Class::ResultSet';

__PACKAGE__->load_components( qw{ Helper::ResultSet::SetOperations } );


sub getCommonNGramsCountByLength {
    my $self = shift;
    my $experimentId = shift;
    my $taskId = shift;

    my @commonNGrams = $self->search( undef, {
        bind => [ $taskId, $experimentId ],
        select => [ 'length', { count => '1', -as => 'count' } ],
        as => [ 'length', 'count' ],
        group_by => [ 'length' ],
    } )->all;

    my @ngrams = [];
    for my $ngramCount ( @commonNGrams ) {
        $ngrams[ $ngramCount->get_column( 'length' ) ] = $ngramCount->get_column( 'count' );
    }

    return \@ngrams;
}

sub searchCommonNGramsCountByLengthAndPosition {
    my $self = shift;
    my $experimentId = shift;
    my $taskId = shift;
    
    return $self->search( undef, {
        bind => [ $taskId, $experimentId ],
        select => [ 'position', 'length', { count => '1', -as => 'count' } ],
        as => [ 'position', 'length', 'count' ],
        group_by => [ 'position', 'length' ],
    } );
}


sub searchMissingNGramsForTasks {
    my $self = shift;
    my $experimentId = shift;
    my $taskA = shift;
    my $taskB = shift;

    my $ngramsForTaskA = $self->search( {}, { bind => [ $taskA, $experimentId ] } );
    my $ngramsForTaskB = $self->search( {}, { bind => [ $taskB, $experimentId ] } );
    
    return $ngramsForTaskB->except( $ngramsForTaskA )->search( undef, {
        select => [ 'text', { count => '1', -as => 'count' } ],
        as => [ 'text', 'count' ],
        group_by => [ 'text' ],
        order_by => { -desc => 'count' },
    } );
}


sub searchRedundantNGramsForTasks {
    my $self = shift;
    my $experimentId = shift;
    my $taskA = shift;
    my $taskB = shift;

    return $self->searchMissingNGrams( $experimentId, $taskB, $taskA );
}
