<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2009 ITEMAN, Inc. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    ITEMAN_GAFilter
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GAFilter_CLI

/**
 * コマンドラインから Google Analytics のトラッキングを行うためのインターフェイス。
 *
 * @package    ITEMAN_GAFilter
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GAFilter_CLI extends Stagehand_CLIController
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    protected $exceptionClass = 'ITEMAN_GAFilter_Exception';
    protected $shortOptions = 'hV';
    protected $longOptions = array('run-as-filter==',
                                   'web-property-id=',
                                   'converters='
                                   );

    /**#@-*/

    /**#@+
     * @access private
     */

    private $_config = array('displayUsage' => false,
                             'displayVersion' => false,
                             'runAsFilter' => false,
                             'webPropertyID' => null,
                             'converters' => array()
                             );

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ createTracker()

    /**
     * @return ITEMAN_GAFilter_Tracker
     */
    public function createTracker()
    {
        return new ITEMAN_GAFilter_Tracker();
    }

    // }}}
    // {{{ createConverter()

    /**
     * @param string $converterClass
     * @return ITEMAN_GAFilter_Converter_ConverterInterface
     */
    public function createConverter($converterClass)
    {
        return new $converterClass();
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    // }}}
    // {{{ doConfigureByOption()

    /**
     * @param string $option
     * @param string $value
     * @return boolean
     */
    protected function doConfigureByOption($option, $value)
    {
        switch ($option) {
        case 'h':
            $this->_config['displayUsage'] = true;
            return false;
        case 'V':
            $this->_config['displayVersion'] = true;
            return false;
        case '--run-as-filter':
            $this->_config['runAsFilter'] = true;
            break;
        case '--web-property-id':
            $this->_config['webPropertyID'] = $value;
            break;
        case '--converters':
            $this->_config['converters'] =
                explode(',', preg_replace('/,$/', '', $value));
            break;
        }

        return true;
    }

    // }}}
    // {{{ doConfigureByArg()

    /**
     * @param string $arg
     * @return boolean
     */
    protected function doConfigureByArg($arg)
    {
        $this->_config['displayUsage'] = true;
        return false;
    }

    // }}}
    // {{{ doRun()

    /**
     * @throws ITEMAN_GAFilter_Exception
     */
    protected function doRun()
    {
        if ($this->_config['displayUsage']) {
            $this->_displayUsage();
            return;
        }

        if ($this->_config['displayVersion']) {
            $this->_displayVersion();
            return;
        }

        $this->_trackPageView();

        if ($this->_config['runAsFilter']) {
            $this->_passOriginalResponse();
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _displayUsage()

    /**
     * 使い方を表示する。
     */
    private function _displayUsage()
    {
        $scriptBaseName = basename($_SERVER['SCRIPT_NAME']);
        $scriptPath = realpath($_SERVER['SCRIPT_NAME']);

        echo "使い方: $scriptBaseName オプション...

オプション:

  -h
     このヘルプを表示します。

  -V
     バージョンを表示します。

  --web-property-id=WEB-PROPERTY-ID
     トラッキングの対象となる Google Analytics プロファイルを識別するための
    「ウェブプロパティID」を指定します。
    「ウェブプロパティID」のフォーマットは UA-XXX-X であり、
     https://www.google.com/analytics/settings/home
     で確認することができます。

  --converters=CONVERTER1,CONVERTER2,...
     デフォルトコンバータのあとに実行するコンバータをひとつ以上指定します。

  --run-as-filter (任意)
     フィルタとして実行します。このコマンドを Apache のフィルタとして動作させる場
     合、このオプションを指定する必要があります。
     Apache のフィルタについては、
     http://httpd.apache.org/docs/2.2/mod/mod_ext_filter.html
     を参照してください。

環境変数:

  SERVER_NAME (utmhn):
     トラッキングの対象となるページの URI のホスト部分を指定します。
     例えば URI が http://www.example.com/foo/bar.tar.gz の場合、ホスト部分は
     www.example.com となります。
     この環境変数は Apache によって自動的に設定されるため、通常は指定する必要は
     ありません。
  
  REQUEST_URI (utmp):
     トラッキングの対象となるページの URI のパス部分を指定します。
     例えば URI が http://www.example.com/foo/bar.tar.gz の場合、パス部分は
     /foo/bar.tar.gz となります。
     この環境変数は Apache によって自動的に設定されるため、通常は指定する必要は
     ありません。
  
  HTTP_USER_AGENT:
     トラッキングの対象となるページにリクエストを行ったユーザエージェントを指定し
     ます。
     この環境変数は Apache によって自動的に設定されるため、通常は指定する必要は
     ありません。

SSI による実行:

  このコマンドを Apache の SSI を使って動作させる場合、下記の exec コマンドをペー
  ジに含める必要があります。

  <!--#exec cmd=\"$scriptPath --web-property-id=WEB-PROPERTY-ID --converters=CONVERTER1,CONVERTER2,...\" -->

  詳細は、
  http://httpd.apache.org/docs/2.2/mod/mod_include.html#element.exec
  を参照してください。
";
    }

    // }}}
    // {{{ _displayVersion()

    /**
     * バージョンを表示する。
     */
    private function _displayVersion()
    {
        echo 'ITEMAN_GAFilter @package_version@

Copyright (c) 2008-2009 ITEMAN, Inc. All rights reserved.
';
    }

    // }}}
    // {{{ _trackPageView()

    /**
     */
    private function _trackPageView()
    {
        $tracker = $this->createTracker();
        $tracker->setWebPropertyID($this->_config['webPropertyID']);

        foreach ($this->_config['converters'] as $converterClass) {
            if (!class_exists($converterClass)) {
                throw new ITEMAN_GAFilter_Exception("指定されたコンバータ [ $converterClass ] が見つかりません");
            }

            $tracker->addConverter($this->createConverter($converterClass));
        }

        $tracker->trackPageView();
    }

    // }}}
    // {{{ _passOriginalResponse()

    /**
     */
    private function _passOriginalResponse()
    {
        $stdin = @fopen('php://stdin', 'r');
        if ($stdin === false) {
            return;
        }

        while (!feof($stdin)) {
            echo fgets($stdin);
        }

        fclose($stdin);
    }

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
