#!/bin/bash

sudo -u www-data -s <<DEPLOY
DIR="/var/www/MT-ComparEval"
cd $DIR

# KILL RUNNING PROCESSES
pkill -f "^php -f .*www/index.php"

# DOWNLOAD LATEST CHANGES
git pull

# DEPLOY DATABASE SCHEMA
rm app/database
sqlite3 app/database < schema.sql

# REMOVE LOCKS FROM DATA SO IT CAN BE IMPORTED AGAIN
find data -name ".*" -exec rm {} \;

# INVALIDATE CACHE
rm -rf temp/*

# RUN BACKGROUND WORKERS
php -f www/index.php Background:Watcher:Watch --folder=data >log/experiments.log 2>&1 &

echo DEPLOY DONE
DEPLOY
