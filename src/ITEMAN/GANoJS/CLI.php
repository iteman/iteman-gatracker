<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2008 ITEMAN, Inc. All rights reserved.
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
 * @package    ITEMAN_GANoJS
 * @copyright  2008 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GANoJS_CLI

/**
 * コマンドラインから Google Analytics のトラッキングを行うためのインターフェイス。
 *
 * @package    ITEMAN_GANoJS
 * @copyright  2008 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GANoJS_CLI extends Stagehand_CLIController
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    protected $exceptionClass = 'ITEMAN_GANoJS_Exception';
    protected $shortOptions = 'hV';
    protected $longOptions = array('web-property-id=');

    /**#@-*/

    /**#@+
     * @access private
     */

    private $_config = array('displayUsage' => false,
                             'displayVersion' => false
                             );
    private $_trackingCode;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ __construct()

    /**
     */
    public function __construct()
    {
        $this->_trackingCode = new ITEMAN_GANoJS_TrackingCode();
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
        case '--web-property-id':
            $this->_config['webPropertyID'] = $value;
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

        foreach ($this->_config as $key => $value) {
            if (method_exists($this->_trackingCode, "set$key")) {
                $this->_trackingCode->{ "set$key" }($value);
            }
        }

        $this->_trackingCode->trackPageView();
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
        echo "使い方: {$_SERVER['SCRIPT_NAME']} オプション...

オプション:

  -h
    このヘルプを表示します。

  -V
    バージョンを表示します。

  --web-property-id=WEB-PROPERTY-ID
    トラッキングの対象となる Google Analytics プロファイルとして WEB-PROPERTY-ID
    を指定します。
    WEB-PROPERTY-ID のフォーマットは UA-XXX-X であり、
    https://www.google.com/analytics/settings/home で確認することができます。
";
    }

    // }}}
    // {{{ _displayVersion()

    /**
     * バージョンを表示する。
     */
    private function _displayVersion()
    {
        echo 'ITEMAN_GANoJS @package_version@

Copyright (c) 2008 ITEMAN, Inc. All rights reserved.
';
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
