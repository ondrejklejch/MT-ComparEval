use strict;
use warnings;
use Test::More;


use Catalyst::Test 'MTComparEval';
use MTComparEval::Controller::Tasks;

ok( request('/tasks')->is_success, 'Request should succeed' );
done_testing();
