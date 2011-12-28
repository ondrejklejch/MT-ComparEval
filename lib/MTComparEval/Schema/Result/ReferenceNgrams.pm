package MTComparEval::Schema::Result::ReferenceNgrams;

use strict;
use warnings;

use base 'DBIx::Class';

__PACKAGE__->load_components("InflateColumn::DateTime", "Core");
__PACKAGE__->table("reference_ngrams");
__PACKAGE__->add_columns(
  "id",
  {
    data_type => "INTEGER",
    default_value => undef,
    is_nullable => 0,
    size => undef,
  },
  "sentence_id",
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
  "length",
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
);
__PACKAGE__->set_primary_key("id");
__PACKAGE__->add_unique_constraint(
  "sentence_id_position_length_unique",
  ["sentence_id", "position", "length"],
);
__PACKAGE__->belongs_to(
  "sentence_id",
  "MTComparEval::Schema::Result::ReferenceSentences",
  { id => "sentence_id" },
);


# Created by DBIx::Class::Schema::Loader v0.04006 @ 2011-12-28 10:50:16
# DO NOT MODIFY THIS OR ANYTHING ABOVE! md5sum:M9nPGM1PkcPK64QcuciClA


# You can replace this text with custom content, and it will be preserved on regeneration
1;
