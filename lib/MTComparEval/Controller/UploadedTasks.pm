package MTComparEval::Controller::UploadedTasks;
use Moose;
use namespace::autoclean;

BEGIN {extends 'Catalyst::Controller'; }
extends 'Catalyst::Controller::FormBuilder';

sub index :Path :Args(0) {
    my ( $self, $c ) = @_;

    $c->stash->{ tasks } = [ $c->model( 'UploadedTasksFiles' )->get_uploaded_tasks_files() ];
}


sub upload :Local :Args(1) Form( '/uploadedtasks/upload' ) {
    my ( $self, $c, $filename ) = @_;

    my $form = $self->formbuilder;
    if( $form->submitted && $form->validate ) {
        my $task = $c->model( 'DBIC::tasks' )->create( {
            name => $form->field( 'name' ),
            comment => $form->field( 'comment' ),
            experiment_id => $form->field( 'experiment_id' )
        } ); 

        my $path = $c->path_to( 'data', 'translation' . $task->id );
        $c->model( 'UploadedTasksFiles' )->move_task_file( $filename, $path );

        my $command = $c->config->{ 'process_translation' } . ' ' . $path . ' ' . $form->field( 'experiment_id' ) . ' ' . $task->id;
        $c->log->info( $command );
        Proc::Simple->new()->start( $command );
        
        $c->flash->{ message } = 'Task "' . $form->field( 'name' ) . '" was created';
        $c->response->redirect( $c->uri_for_action( '/uploadedtasks/index' ) );
    } else {
        my @experiments = map { { $_->id => $_->name } } $c->model( 'DBIC::experiments' )->search()->all;

        $form->field( name => 'experiment_id', options => \@experiments );
    }
}


#__PACKAGE__->meta->make_immutable;

1;
