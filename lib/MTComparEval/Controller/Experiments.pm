package MTComparEval::Controller::Experiments;
use Moose;
use namespace::autoclean;

BEGIN {extends 'Catalyst::Controller'; }

=head1 NAME

MTComparEval::Controller::Experiments - Catalyst Controller

=head1 DESCRIPTION

Catalyst Controller.

=head1 METHODS

=cut


=head2 index

=cut

sub index :Path :Args(0) {
    my ( $self, $c, @args ) = @_;

    $c->stash->{experiments} = $c->model( 'TestDatabase::experiments' )->search({});
}


=head2 new

=cut

sub delete :Local {
    my ( $self, $c, $id ) = @_;
    my $experiment = $c->model( 'TestDatabase::experiments' )->find( { id => $id } );

    if( $experiment ) {
	$c->flash->{ message } = "Experiment " . $experiment->name . " deleted.";
        $experiment->delete;
    } else {
        $c->response->status( 404 );
	$c->flash->{ error } = "Experiment $id not found.";
    } 
 
    $c->response->redirect( $c->uri_for_action( 'experiments/index' ) );
    $c->detach(); 
}

=head1 AUTHOR

A clever guy

=head1 LICENSE

This library is free software. You can redistribute it and/or modify
it under the same terms as Perl itself.

=cut

__PACKAGE__->meta->make_immutable;

1;
