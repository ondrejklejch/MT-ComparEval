#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $DIR/..

sudo -u www-data -s <<DEPLOY

# DOWNLOAD LATEST CHANGES
git pull

# DEPLOY DATABASE SCHEMA
rm app/database
sqlite3 app/database < schema.sql

# REMOVE LOCKS FROM DATA SO IT CAN BE IMPORTED AGAIN
find data -name ".*" -exec rm {} \;

# SET WRITE PERMISSIONS
chmod -R 777 log/ temp/

# RUN BACKGROUND WORKERS
php -f www/index.php Background:Watcher:Watch --folder=data >log/import.log 2>&1 &

echo DEPLOY DONE

DEPLOY
