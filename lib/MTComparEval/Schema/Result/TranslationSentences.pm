package MTComparEval::Schema::Result::TranslationSentences;

use strict;
use warnings;

use base 'DBIx::Class';

__PACKAGE__->load_components("InflateColumn::DateTime", "Core");
__PACKAGE__->table("translation_sentences");
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
  "task_id",
  {
    data_type => "INTEGER",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "position",
  {
    data_type => "INTEGER",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "text",
  {
    data_type => "TEXT",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "length",
  {
    data_type => "INTEGER",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "diff_bleu",
  {
    data_type => "REAL",
    default_value => undef,
    is_nullable => 1,
    size => undef,
  },
);
__PACKAGE__->set_primary_key("id");
__PACKAGE__->add_unique_constraint(
  "experiment_id_task_id_position_unique",
  ["experiment_id", "task_id", "position"],
);
__PACKAGE__->belongs_to(
  "experiment_id",
  "MTComparEval::Schema::Result::Experiments",
  { id => "experiment_id" },
);
__PACKAGE__->belongs_to(
  "task_id",
  "MTComparEval::Schema::Result::Tasks",
  { id => "task_id" },
);


# Created by DBIx::Class::Schema::Loader v0.04006 @ 2011-12-28 10:50:16
# DO NOT MODIFY THIS OR ANYTHING ABOVE! md5sum:BEhacpkqzaUcjzWv9tFn+g


# You can replace this text with custom content, and it will be preserved on regeneration
1;
