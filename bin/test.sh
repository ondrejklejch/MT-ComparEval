#!/bin/bash

trap 'pkill -f "php -f www/index"; pkill -f "php -S"; pkill -f "webdriver"' SIGINT SIGTERM EXIT

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
$DIR/server.sh 2>$DIR/../log/server.error.log >$DIR/../log/server.log &
$DIR/webdriver.sh > $DIR/../log/webdriver.log 2> $DIR/../log/webdriver.error.log & 

rm -rf $DIR/../test_data
mkdir $DIR/../test_data

$DIR/behat --no-paths $@
