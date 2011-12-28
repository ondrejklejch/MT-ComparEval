package MTComparEval::Schema::Result::Tasks;

use strict;
use warnings;

use base 'DBIx::Class';

__PACKAGE__->load_components("InflateColumn::DateTime", "Core");
__PACKAGE__->table("tasks");
__PACKAGE__->add_columns(
  "id",
  {
    data_type => "INTEGER",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "experiment_id",
  {
    data_type => "INTEGER",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "name",
  {
    data_type => "TEXT",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "bleu",
  {
    data_type => "REAL",
    default_value => undef,
    is_nullable => 1,
    size => undef,
  },
  "state",
  { data_type => "INTEGER", default_value => 0, is_nullable => 1, size => undef },
  "comment",
  {
    data_type => "TEXT",
    default_value => undef,
    is_nullable => 1,
    size => undef,
  },
  "date",
  {
    data_type => "DATETIME",
    default_value => "DATETIME( 'now', 'localtime' )",
    is_nullable => 1,
    size => undef,
  },
);
__PACKAGE__->set_primary_key("id");
__PACKAGE__->belongs_to(
  "experiment_id",
  "MTComparEval::Schema::Result::Experiments",
  { id => "experiment_id" },
);
__PACKAGE__->has_many(
  "translation_sentences",
  "MTComparEval::Schema::Result::TranslationSentences",
  { "foreign.task_id" => "self.id" },
);


# Created by DBIx::Class::Schema::Loader v0.04006 @ 2011-12-28 12:27:21
# DO NOT MODIFY THIS OR ANYTHING ABOVE! md5sum:RQH8uWp35CJDC7pxXfD5BQ


# You can replace this text with custom content, and it will be preserved on regeneration
1;
