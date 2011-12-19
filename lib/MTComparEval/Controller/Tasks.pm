package MTComparEval::Controller::Tasks;
use Moose;
use namespace::autoclean;

BEGIN {extends 'Catalyst::Controller'; }

=head1 NAME

MTComparEval::Controller::Tasks - Catalyst Controller

=head1 DESCRIPTION

Catalyst Controller.

=head1 METHODS

=cut


=head2 index

=cut

sub index :Path :Args(1) {
    my ( $self, $c, $experimentId ) = @_;

    my $experiment = $c->model( 'TestDatabase::experiments' )->find( { id => $experimentId } );
    if( !$experiment ) {
        $c->flash->{ error } = 'Experiment ' . $experimentId . ' not found.';
        $c->response->redirect( $c->uri_for_action( 'experiments/index' ) );
        $c->detach();
    } else {
        $c->stash->{ tasks } = $c->model( 'TestDatabase::tasks' )->search( { experiment_id => $experimentId } );
        $c->stash->{ experiment } = $experiment;
    }
}


=head1 AUTHOR

A clever guy

=head1 LICENSE

This library is free software. You can redistribute it and/or modify
it under the same terms as Perl itself.

=cut

# __PACKAGE__->meta->make_immutable;

1;
