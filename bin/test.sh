#!/bin/bash

set +e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
$DIR/server.sh >/dev/null 2>/dev/null &
SERVER=$!
$DIR/webdriver.sh > /dev/null 2> /dev/null & 
WEBDRIVER=$!

$DIR/behat --tags=~@skipped

kill $SERVER
kill $WEBDRIVER

