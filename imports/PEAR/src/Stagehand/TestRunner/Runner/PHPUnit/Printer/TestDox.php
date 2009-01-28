<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2008-2009 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
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
 * @package    Stagehand_TestRunner
 * @copyright  2008-2009 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: TestDox.php 210 2009-01-27 12:42:15Z iteman $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.4.0
 */

require_once 'PHPUnit/Util/TestDox/ResultPrinter/Text.php';
require_once 'PHPUnit/Framework/Test.php';
require_once 'PHPUnit/Framework/AssertionFailedError.php';
require_once 'Stagehand/TestRunner/Runner/PHPUnit/TestDox/NamePrettifier.php';

// {{{ Stagehand_TestRunner_Runner_PHPUnit_Printer_TestDox

/**
 * A result printer for TestDox documentation.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2008-2009 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 2.6.2
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 2.4.0
 */
class Stagehand_TestRunner_Runner_PHPUnit_Printer_TestDox extends PHPUnit_Util_TestDox_ResultPrinter_Text
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

    private $_lastTestFailed = false;
    private $_color;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ __construct()

    /**
     * Constructor.
     *
     * @param  resource  $out
     * @param  boolean   $color
     */
    public function __construct($out = NULL, $color)
    {
        parent::__construct($out);
        $this->_color = $color;

        if ($this->_color) {
            include_once 'Stagehand/TestRunner/Coloring.php';
        }

        $this->prettifier =
            new Stagehand_TestRunner_Runner_PHPUnit_TestDox_NamePrettifier();
    }

    // }}}
    // {{{ addError()

    /**
     * An error occurred.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if ($this->_color) {
            $this->write(Stagehand_TestRunner_Coloring::magenta(" - {$this->currentTestMethodPrettified}\n"));
        } else {
            $this->write(" - {$this->currentTestMethodPrettified}\n");
        }

        $this->_lastTestFailed = true;
    }

    // }}}
    // {{{ addFailure()

    /**
     * A failure occurred.
     *
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if ($this->_color) {
            $this->write(Stagehand_TestRunner_Coloring::red(" - {$this->currentTestMethodPrettified}\n"));
        } else {
            $this->write(" - {$this->currentTestMethodPrettified}\n");
        }

        $this->_lastTestFailed = true;
    }

    // }}}
    // {{{ addIncompleteTest()

    /**
     * Incomplete test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if ($this->_color) {
            $this->write(Stagehand_TestRunner_Coloring::yellow(" - {$this->currentTestMethodPrettified}\n"));
        } else {
            $this->write(" - {$this->currentTestMethodPrettified}\n");
        }

        $this->_lastTestFailed = true;
    }

    // }}}
    // {{{ addSkippedTest()

    /**
     * Skipped test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if ($this->_color) {
            $this->write(Stagehand_TestRunner_Coloring::yellow(" - {$this->currentTestMethodPrettified}\n"));
        } else {
            $this->write(" - {$this->currentTestMethodPrettified}\n");
        }

        $this->_lastTestFailed = true;
    }

    // }}}
    // {{{ endTest()

    /**
     * A test ended.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if (!$this->_lastTestFailed) {
            if ($this->_color) {
                $this->write(Stagehand_TestRunner_Coloring::green(" - {$this->currentTestMethodPrettified}\n"));
            } else {
                $this->write(" - {$this->currentTestMethodPrettified}\n");
            }
        }

        $this->_lastTestFailed = false;
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    // }}}
    // {{{ doEndClass()

    /**
     */
    protected function doEndClass()
    {
        $this->endClass($this->testClass);
    }

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
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
