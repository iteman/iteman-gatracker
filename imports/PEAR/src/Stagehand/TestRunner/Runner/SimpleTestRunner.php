<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2007 Masahiko Sakamoto <msakamoto-sf@users.sourceforge.net>,
 *               2007-2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2007 Masahiko Sakamoto <msakamoto-sf@users.sourceforge.net>
 * @copyright  2007-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 2.9.0
 * @link       http://simpletest.org/
 * @since      File available since Release 2.1.0
 */

require_once 'simpletest/reporter.php';

// {{{ Stagehand_TestRunner_Runner_SimpleTestRunner

/**
 * A test runner for SimpleTest.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2007 Masahiko Sakamoto <msakamoto-sf@users.sourceforge.net>
 * @copyright  2007-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 2.9.0
 * @link       http://simpletest.org/
 * @since      Class available since Release 2.1.0
 */
class Stagehand_TestRunner_Runner_SimpleTestRunner extends Stagehand_TestRunner_Runner
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
     * Runs tests based on the given TestSuite object.
     *
     * @param TestSuite $suite
     * @param stdClass  $config
     */
    public function run($suite, $config)
    {
        $reporter = new TextReporter();
        ob_start();
        $suite->run($reporter);
        $output = ob_get_contents();
        ob_end_clean();

        if ($config->usesGrowl) {
            if (preg_match('/^(OK.+)/ms', $output, $matches)) {
                $this->notification->name = 'Green';
                $this->notification->description = $matches[1];
            } elseif (preg_match('/^(FAILURES.+)/ms', $output, $matches)) {
                $this->notification->name = 'Red';
                $this->notification->description = $matches[1];
            }
        }

        if ($config->colors) {
            print Console_Color::convert(preg_replace(array('/^(OK.+)/ms',
                                                            '/^(FAILURES!!!.+)/ms',
                                                            '/^(\d+\)\s)(.+at \[.+\]$\s+in .+)$/m',
                                                            '/^(Exception \d+!)/m',
                                                            '/^(Unexpected exception of type \[.+\] with message \[.+\] in \[.+\]$\s+in .+)$/m'),
                                                      array('%g$1%n',
                                                            '%r$1%n',
                                                            "\$1%r\$2%n",
                                                            '%p$1%n',
                                                            '%p$1%n'),
                                                      Console_Color::escape($output))
                                         );
        } else {
            print $output;
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
