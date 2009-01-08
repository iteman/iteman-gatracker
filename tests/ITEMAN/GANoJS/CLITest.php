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

// {{{ ITEMAN_GANoJS_CLITest

/**
 * ITEMAN_GANoJS_CLI のためのテスト。
 *
 * @package    ITEMAN_GANoJS
 * @copyright  2008 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GANoJS_CLITest extends PHPUnit_Framework_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    /**
     * @test
     */
    public function 使い方を表示する()
    {
        $_SERVER['SCRIPT_NAME'] = 'bin/iteman-ganojs';
        $GLOBALS['argv'] = array($_SERVER['SCRIPT_NAME'], '-h');
        $GLOBALS['argc'] = count($_SERVER['argv']);

        $cli = new ITEMAN_GANoJS_CLI();
        ob_start();
        $result = $cli->run();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(0, $result);
        $this->assertRegExp("!^使い方: {$_SERVER['SCRIPT_NAME']}!", $content);
    }

    /**
     * @test
     */
    public function バージョンを表示する()
    {
        $_SERVER['SCRIPT_NAME'] = 'bin/iteman-ganojs';
        $GLOBALS['argv'] = array($_SERVER['SCRIPT_NAME'], '-V');
        $GLOBALS['argc'] = count($_SERVER['argv']);

        $cli = new ITEMAN_GANoJS_CLI();
        ob_start();
        $result = $cli->run();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(0, $result);
        $this->assertRegExp('/^ITEMAN_GANoJS @package_version@/', $content);
    }

    /**
     * @test
     */
    public function 実行に必要なオプションが不足している場合メッセージを表示しエラーにする()
    {
        $_SERVER['SCRIPT_NAME'] = 'bin/iteman-ganojs';
        $GLOBALS['argv'] = array($_SERVER['SCRIPT_NAME']);
        $GLOBALS['argc'] = count($_SERVER['argv']);

        $cli = new ITEMAN_GANoJS_CLI();
        ob_start();
        $result = $cli->run();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, $result);
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

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
