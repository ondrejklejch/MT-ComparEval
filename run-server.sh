#!/bin/sh

if [ ! -f app/database ]; then
	sqlite3 app/database < schema.sql
fi;

php5 -S localhost:8000 -t www/
