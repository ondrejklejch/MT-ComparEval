package MTComparEval::Controller::Task;
use Moose;
use NGram;
use namespace::autoclean;

BEGIN {extends 'Catalyst::Controller'; }

=head1 NAME

MTComparEval::Controller::Task - Catalyst Controller

=head1 DESCRIPTION

Catalyst Controller.

=head1 METHODS

=cut


=head2 index

=cut

sub index :Path :Args(0) {
    my ( $self, $c ) = @_;

    $c->response->body('Matched MTComparEval::Controller::Task in Task.');
}


=head2 detail

=cut

sub detail :Local :Args(1) {
    my( $self, $c, $id ) = @_;

    my $task = $c->model( 'DBIC::tasks' )->find( { id => $id } );
    my $experiment_id = $task->get_column( 'experiment_id' );
    my $experiment = $c->model( 'DBIC::experiments' )->find( { id => $experiment_id } );
    my $sourceSentences = $experiment->search_related( 'source_sentences' );
    my $referenceSentences = $experiment->search_related( 'reference_sentences' );
    my $translationSentences = $task->search_related( 'translation_sentences' );
    
    my @sentences;
    while( my $translation = $translationSentences->next() ) {
        my $source = $sourceSentences->next();
        my $reference = $referenceSentences->next();

        my %sentence = (
            'src' => $source->get_column( 'text' ),
            'ref' => $reference->get_column( 'text' ),
            'tst' => $translation->get_column( 'text' ),
            'bleu' => $translation->get_column( 'diff_bleu' ),
        );

        push @sentences, \%sentence;
    }

    $c->stash->{ 'bleu' } = $task->get_column( 'bleu' );
    $c->stash->{ 'sentences' } = \@sentences;
}


=head1 AUTHOR

A clever guy

=head1 LICENSE

This library is free software. You can redistribute it and/or modify
it under the same terms as Perl itself.

=cut

__PACKAGE__->meta->make_immutable;

1;
