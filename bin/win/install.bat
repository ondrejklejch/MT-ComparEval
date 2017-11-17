cd ..\..
"%programfiles%\sqlite\sqlite3" storage/database < schema.sql
mkdir data
composer update
pause