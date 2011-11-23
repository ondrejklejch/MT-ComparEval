use strict;
use warnings;

use MTComparEval;

my $app = MTComparEval->apply_default_middlewares(MTComparEval->psgi_app);
$app;

