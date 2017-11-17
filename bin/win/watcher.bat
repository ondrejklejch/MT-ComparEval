set path=%path%;%programfiles(x86)%\GnuWin32\bin;%programfiles%\Python27
cd ..\..
php -f www/index.php Background:Watcher:Watch --folder=./data
pause