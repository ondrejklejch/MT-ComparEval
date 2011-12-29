package MTComparEval::Controller::Experiments;
use Moose;
use Proc::Simple;
use namespace::autoclean;
use File::Remove 'remove';

BEGIN {extends 'Catalyst::Controller'; }
extends 'Catalyst::Controller::FormBuilder';

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

    $c->stash->{experiments} = $c->model( 'DBIC::experiments' )->search({});
}


=head2 edit

=cut

sub edit :Local Form {
    my ( $self, $c, $id ) = @_;
    my $form = $self->formbuilder;
    my $experiment = $c->model( 'DBIC::experiments' )->find_or_new( { id => $id } );

    if( !$id ) {
        $form->field( name => 'source', required => 1 );
        $form->field( name => 'reference', required => 1 );
    }

    if( $form->submitted && $form->validate ) {
        $experiment->name( $form->field( 'name' ) );
        $experiment->comment( $form->field( 'comment' ) );
        $experiment->update_or_insert;

        for my $type ( 'source', 'reference' ) {
            if( $form->field( $type ) ) {
                my $file = $c->req->upload( $type );
                my $path = $c->path_to( 'data', $type . $experiment->id );
                $file->copy_to( $path );

                my $command = $c->config->{ 'process_' . $type } . ' ' . $path . ' ' . $experiment->id;
                $c->log->info( $command );
                my $process = Proc::Simple->new();
                $process->start( $command );
            }
        }

        if( !$id ) {
            $c->flash->{ message } = 'Experiment "' . $form->field( 'name' ) . '" was created';
        } else {
            $c->flash->{ message } = 'Experiment "' . $form->field( 'name' ) . '" was updated';
        }

        $c->response->redirect( $c->uri_for_action( 'experiments/index' ) );
        $c->detach();
    } else {
        if( !$id ) {
            $c->stash->{ action } = 'Adding new experiment';
        } else {
            $c->stash->{ action } = 'Edit experiment ' . $experiment->name;
        }
    
        $form->field( name => 'name', value => $experiment->name );
        $form->field( name => 'comment', value => $experiment->comment );
    }
}



=head2 delete 

=cut

sub delete :Local {
    my ( $self, $c, $id ) = @_;
    my $experiment = $c->model( 'DBIC::experiments' )->find( { id => $id } );

    if( $experiment ) {
        $c->flash->{ message } = "Experiment " . $experiment->name . " deleted.";
        remove( $c->path_to( 'data', 'source' . $id ) );
        remove( $c->path_to( 'data', 'reference' . $id ) );
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

# __PACKAGE__->meta->make_immutable;

1;
