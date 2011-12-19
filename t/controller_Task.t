use strict;
use warnings;
use Test::More;


use Catalyst::Test 'MTComparEval';
use MTComparEval::Controller::Task;

ok( request('/task')->is_success, 'Request should succeed' );
done_testing();
