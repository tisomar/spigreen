@echo off
%~d0
cd %~p0
set GO_TO=%~dp0
set PROPEL_VENDOR=%GO_TO%\vendor\propel\propel1\generator\bin\propel-gen %GO_TO%\qcommerce\propel main
%PROPEL_VENDOR%
chdir /d %GO_TO% &rem restore current directory