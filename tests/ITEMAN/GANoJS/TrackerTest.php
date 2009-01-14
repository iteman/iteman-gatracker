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

// {{{ ITEMAN_GANoJS_TrackerTest

/**
 * ITEMAN_GANoJS_Tracker のためのテスト。
 *
 * @package    ITEMAN_GANoJS
 * @copyright  2008 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GANoJS_TrackerTest extends PHPUnit_Framework_TestCase
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
    public function トラッキングUriを生成する()
    {
        $_SERVER['ITEMAN_GANOJS_WEBPROPERTYID'] = 'UA-6415151-2';
        $_SERVER['SERVER_NAME'] = 'iteman.jp';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; U; Linux i686; ja; rv:1.9.0.5) Gecko/2008121622 Ubuntu/8.10 (intrepid) Firefox/3.0.5';
        $_SERVER['REQUEST_URI'] = '/blog/';

        $adapter = new HTTP_Request2_Adapter_Mock();
        $adapter->addResponse('HTTP/1.1 200 OK');
        $request = new HTTP_Request2();
        $request->setAdapter($adapter);
        $tracker = $this->getMock('ITEMAN_GANoJS_Tracker', array('createHTTPRequest'));
        $tracker->expects($this->any())
                ->method('createHTTPRequest')
                ->will($this->returnValue($request));

        $tracker->trackPageView();

        $headers = $request->getHeaders();

        $this->assertEquals($_SERVER['HTTP_USER_AGENT'], $headers['user-agent']);

        $url = $request->getUrl();

        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('www.google-analytics.com', $url->getHost());
        $this->assertEquals('/__utm.gif', $url->getPath());

        $queryVariables = array();
        foreach (explode('&', $url->getQuery()) as $queryVariable) {
            list($name, $value) = explode('=', $queryVariable);
            $queryVariables[$name] = $value;
        }

        $this->assertEquals('4.3', $queryVariables['utmwv']);
        $this->assertGreaterThanOrEqual(0, $queryVariables['utmn']);
        $this->assertLessThanOrEqual(2147483647, $queryVariables['utmn']);
        $this->assertEquals($_SERVER['SERVER_NAME'], $queryVariables['utmhn']);
        $this->assertEquals('UTF-8', $queryVariables['utmcs']);
        $this->assertEquals('-', $queryVariables['utmsr']);
        $this->assertEquals('-', $queryVariables['utmsc']);
        $this->assertEquals('-', $queryVariables['utmul']);
        $this->assertEquals('0', $queryVariables['utmje']);
        $this->assertEquals('-', $queryVariables['utmfl']);
        $this->assertEquals('-', $queryVariables['utmdt']);
        $this->assertGreaterThanOrEqual(0, $queryVariables['utmhid']);
        $this->assertLessThanOrEqual(2147483647, $queryVariables['utmhid']);
        $this->assertEquals('-', $queryVariables['utmr']);
        $this->assertEquals($_SERVER['REQUEST_URI'], $queryVariables['utmp']);
        $this->assertEquals($_SERVER['ITEMAN_GANOJS_WEBPROPERTYID'],
                            $queryVariables['utmac']
                            );
        $this->assertRegExp('/^__utma%3D\d+\.\d+\.\d+\.\d+\.\d+.2%3B%2B__utmb%3D\d+%3B%2B__utmc%3D\d+%3B%2B__utmz%3D\d+\.\d+\.2\.2\.utmccn%3D\(direct\)%7Cutmcsr%3D\(direct\)%7Cutmcmd%3D\(none\)%3B$/',
                            $queryVariables['utmcc']
                            );
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
