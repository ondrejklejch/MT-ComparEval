package MTComparEval::Schema::ResultSet::Tasks;

use strict;
use warnings;

use base 'DBIx::Class::ResultSet';


sub getTranslationLength {
    my $self = shift;
    my $taskId = shift;

    my $task = $self->find( { id => $taskId } );
    my $sentences = $task->search_related( 'translation_sentences' );

    return $sentences->search( undef, {
        select => [ { sum => 'length', -as => 'length' } ],
        as => [ 'length' ]
    } )->first->get_column( 'length' ) || 0;
}


sub getTranslationsLengthByPosition {
    my $self = shift;
    my $taskId = shift;

    my $task = $self->find( { id => $taskId } );
    my $sentences = $task->search_related( 'translation_sentences' );

    return $sentences->search( undef, {
        select => [ 'position', 'length' ],
        as => [ 'position', 'length' ]
    } )
}

1;
