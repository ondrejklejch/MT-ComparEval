#!/usr/bin/env perl

use strict;
use warnings;
use FindBin qw( $Bin );
use Path::Class;
use lib dir( $Bin, '..', 'lib' )->stringify;
use MTComparEval::Schema::TestDatabase;
use Config::JFDI;

sub model {
    my $tableName = shift;

    my $filename = file( $Bin, '..', 'mtcompareval.conf' );
    my $home = file( $Bin, '..' );
    my $config = Config::JFDI->new( {
        path => $filename->stringify,
        path_to => $home->stringify
    } );
    my $dsn = $config->get->{ 'Model::MTComparEval' }->{ connect_info };
    my $schema = MTComparEval::Schema::TestDatabase->connect( $dsn ) or die $!;

    return $schema->resultset( $tableName );
}

1;
