package MTComparEval::Schema::Result::CommonNGrams;

use strict;
use warnings;

use base 'DBIx::Class::Core';

__PACKAGE__->table_class( 'DBIx::Class::ResultSource::View' );
__PACKAGE__->table( 'common' );
__PACKAGE__->result_source_instance->is_virtual( 1 );
__PACKAGE__->result_source_instance->view_definition( "
    SELECT s.position, n.length, n.text FROM translation_ngrams AS n
    JOIN translation_sentences AS s ON n.sentence_id = s.id
    WHERE s.task_id = ?
    INTERSECT SELECT s.position, n.length, n.text FROM reference_ngrams AS n
    JOIN reference_sentences AS s ON n.sentence_id = s.id
    WHERE s.experiment_id = ?
" );

__PACKAGE__->add_columns(
    'position' => {
        data_type => 'integer',
    },
    'length' => {
        data_type => 'integer',
    },
    'text' => {
        data_type => 'text',
    },
);


1;
