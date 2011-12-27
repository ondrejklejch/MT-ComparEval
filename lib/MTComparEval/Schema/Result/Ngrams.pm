package MTComparEval::Schema::Result::Ngrams;

use strict;
use warnings;

use base 'DBIx::Class';

__PACKAGE__->load_components("InflateColumn::DateTime", "Core");
__PACKAGE__->table("ngrams");
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
  "MTComparEval::Schema::Result::Sentences",
  { id => "sentence_id" },
);


# Created by DBIx::Class::Schema::Loader v0.04006 @ 2011-12-27 16:19:06
# DO NOT MODIFY THIS OR ANYTHING ABOVE! md5sum:pM4i1tEEF/HkkeH+DJxy+g


# You can replace this text with custom content, and it will be preserved on regeneration
1;
