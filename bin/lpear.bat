@ECHO OFF
REM Copyright (c) 2009 ITEMAN, Inc. All rights reserved.
REM
REM Redistribution and use in source and binary forms, with or without
REM modification, are permitted provided that the following conditions are met:
REM
REM     * Redistributions of source code must retain the above copyright
REM       notice, this list of conditions and the following disclaimer.
REM     * Redistributions in binary form must reproduce the above copyright
REM       notice, this list of conditions and the following disclaimer in the
REM       documentation and/or other materials provided with the distribution.
REM
REM THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
REM AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
REM IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
REM ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
REM LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
REM CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
REM SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
REM INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
REM CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
REM ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
REM POSSIBILITY OF SUCH DAMAGE.

REM *************************************************************
REM ** A pear command wrapper for project local PEAR environments for Windows based systems (based on symfony.bat)
REM *************************************************************

REM This script will do the following:
REM - check for PHP_COMMAND env, if found, use it.
REM   - if not found detect php, if found use it, otherwise err and terminate

IF "%OS%"=="Windows_NT" @SETLOCAL

REM %~dp0 is expanded pathname of the current script under NT
SET SCRIPT_DIR=%~dp0

pushd "%SCRIPT_DIR%.."
FOR /f "usebackq" %%p IN (`chdir`) DO SET TARGET_PATH=%%p
popd

SET PHP_PEAR_INSTALL_DIR=%TARGET_PATH%\imports\PEAR
SET PHP_PEAR_BIN_DIR=%TARGET_PATH%\bin

IF NOT EXIST %SCRIPT_DIR%pear.ini (
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set bin_dir %TARGET_PATH%\bin user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set doc_dir %PHP_PEAR_INSTALL_DIR%\docs user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set php_dir %PHP_PEAR_INSTALL_DIR% user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set cache_dir %TARGET_PATH%\tmp\pear\cache user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set cfg_dir %PHP_PEAR_INSTALL_DIR%\cfg user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set data_dir %PHP_PEAR_INSTALL_DIR%\data user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set download_dir %TARGET_PATH%\tmp\pear\download user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set temp_dir %TARGET_PATH%\tmp\pear\temp user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set test_dir %PHP_PEAR_INSTALL_DIR%\tests user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set www_dir %PHP_PEAR_INSTALL_DIR%\www user
)

@call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini %1 %2 %3 %4 %5 %6 %7 %8 %9

@ECHO OFF

IF "%OS%"=="Windows_NT" @ENDLOCAL
REM PAUSE

REM Local Variables:
REM mode: bat-generic
REM coding: iso-8859-1
REM indent-tabs-mode: nil
REM End:
