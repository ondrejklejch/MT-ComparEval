@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vendor/nette/tester/src/tester
php "%BIN_TARGET%" %*
