<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: PHPUnit.php 194 2008-10-15 13:21:50Z iteman $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.1.0
 */

define('PHPUnit_MAIN_METHOD', 'Stagehand_TestRunner_PHPUnit::run');

require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'Stagehand/TestRunner/Runner/Common.php';
require_once 'Stagehand/TestRunner/Runner/PHPUnit/Printer/Result.php';
require_once 'PHPUnit/TextUI/ResultPrinter.php';
require_once 'Stagehand/TestRunner/Runner/PHPUnit/TestDox.php';
require_once 'Stagehand/TestRunner/Runner/PHPUnit/TestDox/Stream.php';
require_once 'Stagehand/TestRunner/Runner/PHPUnit/Printer/TestDox.php';
require_once 'Stagehand/TestRunner/Runner/PHPUnit/Printer/Progress.php';
require_once 'Stagehand/TestRunner/Runner/PHPUnit/Printer/DetailedProgress.php';

// {{{ Stagehand_TestRunner_Runner_PHPUnit

/**
 * A test runner for PHPUnit.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 2.6.0
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 2.1.0
 */
class Stagehand_TestRunner_Runner_PHPUnit extends Stagehand_TestRunner_Runner_Common
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

    // }}}
    // {{{ run()

    /**
     * Runs tests based on the given PHPUnit_Framework_TestSuite object.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @param stdClass                    $config
     */
    public function run($suite, $config)
    {
        $printer =
            new Stagehand_TestRunner_Runner_PHPUnit_Printer_Result(null,
                                                                   false,
                                                                   $config->color
                                                                   );

        $listeners = array(new Stagehand_TestRunner_Runner_PHPUnit_Printer_TestDox('testdox://', $config->color));
        if (!$config->isVerbose) {
            $listeners[] = new Stagehand_TestRunner_Runner_PHPUnit_Printer_Progress(null,
                                                                                    false,
                                                                                    $config->color
                                                                                    );
        } else {
            $listeners[] = new Stagehand_TestRunner_Runner_PHPUnit_Printer_DetailedProgress(null,
                                                                                            false,
                                                                                            $config->color
                                                                                            );
        }

        $result = PHPUnit_TextUI_TestRunner::run($suite,
                                                 array('printer' => $printer,
                                                       'listeners' => $listeners)
                                                 );

        if ($config->useGrowl) {
            ob_start();
            $printer->printResult($result);
            $output = ob_get_contents();
            ob_end_clean();

            if (preg_match('/^(?:\x1b\[3[23]m)?(OK[^\x1b]+)/ms', $output, $matches)) {
                $this->_notification->name = 'Green';
                $this->_notification->description = $matches[1];
            } elseif (preg_match('/^(FAILURES!\s)(?:\x1b\[31m)?([^\x1b]+)/ms', $output, $matches)) {
                $this->_notification->name = 'Red';
                $this->_notification->description = "{$matches[1]}{$matches[2]}";
            }
        }
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
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
