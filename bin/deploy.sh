#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $DIR/..

# DOWNLOAD LATEST CHANGES
git pull

# DEPLOY DATABASE SCHEMA
rm app/database
sqlite3 app/database < schema.sql

# REMOVE LOCKS FROM DATA SO IT CAN BE IMPORTED AGAIN
find data -name ".*" -exec rm {} \;

# INVALIDATE CACHE
rm -rf temp/cache/*

# SET WRITE PERMISSIONS
chmod -R 777 log/ temp/

# RUN BACKGROUND WORKERS
php -f www/index.php Background:Experiments:Watch --folder=data >log/experiments.log 2>&1 &
php -f www/index.php Background:Tasks:Watch --folder=data >log/tasks.log 2>&1 &

# RUN SERVER
./bin/server.sh >log/server.log 2>&1 &

echo DEPLOY DONE
