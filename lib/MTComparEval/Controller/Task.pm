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


sub loadSentences {
    my( $filename ) = @_;
    open my $file, '<', $filename or die "$filename not exits";

    my @senteces = ();
    while( <$file> ) {
        chomp;
        push @senteces, $_;
    }

    return @senteces;
}


sub get_bleu_for_sentence {
	my ( $ref, $tst ) = @_;

	my $ngram = new NGram;
	$ngram->add_sentence( $ref, $tst );

	return sprintf("%.4f", $ngram->get_sentence_bleu() );
}


=head2 detail

=cut

sub detail :Local :Args(1) {
    my( $self, $c, $id ) = @_;

    my $task = $c->model( 'DBIC::tasks' )->find( { id => $id } );
    my $experiment = $c->model( 'DBIC::experiments' )->find( { id => $task->experiment_id } );
    my @sourceSentences = loadSentences( $c->path_to( 'data', 'source' . $experiment->id ) );
    my @referenceSentences = loadSentences( $c->path_to( 'data', 'reference' . $experiment->id ) );
    my @translationSentences = loadSentences( $c->path_to( 'data', 'translation' . $task->id ) );
    
    my $ngrams = new NGram;
    my @sentences = ();
    while( $#sourceSentences >= 0 ) {
        my $source = pop @sourceSentences;
        my $reference = pop @referenceSentences;
        my $translation = pop @translationSentences;

        $ngrams->add_sentence( $reference, $translation );

        my %sentence = (
            'src' => $source,
            'ref' => $reference,
            'tst' => $translation,
            'bleu' => get_bleu_for_sentence( $reference, $translation ),
        );

        push @sentences, \%sentence;
    }

    $c->stash->{ 'bleu' } = $ngrams->get_bleu();
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
