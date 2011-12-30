package MTComparEval::Model::UploadedTasksFiles;
use Moose;
use namespace::autoclean;
use File::Util;

extends 'Catalyst::Model';

__PACKAGE__->config( {
    root => 'upload',
} );

sub get_uploaded_tasks_files {
    my $self = shift;
    my $file_util = File::Util->new();

    my @translations;
    my @files = $file_util->list_dir( $self->config->{ root }, qw/ --files-only/ );
    for my $file ( @files ) {
        my $path =  $self->config->{ root } . '/' . $file; 
        push( @translations, {
		'name' => $file,
		'path' => $path,
                'last_modified' => $file_util->last_modified( $path ),
        } ) unless $file =~ /^\./;
    }

    @translations = reverse sort { $a->{ last_modified } <=> $b->{ last_modified} } @translations;
    return @translations;
}


sub move_task_file {
    my $self = shift;
    my $filename = shift;
    my $destination = shift;
    my $source = $self->config->{ root } . '/' . $filename;

    rename $source, $destination; 
}

1;
