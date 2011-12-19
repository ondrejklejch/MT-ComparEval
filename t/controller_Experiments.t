use strict;
use warnings;
use Test::More;


use Catalyst::Test 'MTComparEval';
use MTComparEval::Controller::Experiments;

ok( request('/experiments')->is_success, 'Request should succeed' );
done_testing();
