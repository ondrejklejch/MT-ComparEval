use strict;
use warnings;
use Test::More;


use Catalyst::Test 'MTComparEval';
use MTComparEval::Controller::UploadedTasks;

ok( request('/uploadedtasks')->is_success, 'Request should succeed' );
done_testing();
