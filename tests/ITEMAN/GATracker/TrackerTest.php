<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2008-2010 ITEMAN, Inc. All rights reserved.
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
 * @package    ITEMAN_GATracker
 * @copyright  2008-2010 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GATracker_TrackerTest

/**
 * ITEMAN_GATracker_Tracker のためのテスト。
 *
 * @package    ITEMAN_GATracker
 * @copyright  2008-2010 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GATracker_TrackerTest extends PHPUnit_Framework_TestCase
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

    private $_request;
    private static $_webPropertyID = 'UA-6415151-2';

    /**#@-*/

    /**#@+
     * @access public
     */

    public function setUp()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; U; Linux i686; ja; rv:1.9.0.5) Gecko/2008121622 Ubuntu/8.10 (intrepid) Firefox/3.0.5';
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';
        $_SERVER['REQUEST_URI'] = '/blog/';
        $_SERVER['SERVER_NAME'] = 'www.example.com';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja,en-us;q=0.7,en;q=0.3';

        $adapter = new HTTP_Request2_Adapter_Mock();
        $adapter->addResponse('HTTP/1.1 200 OK');
        $this->_request = new HTTP_Request2();
        $this->_request->setAdapter($adapter);
    }

    /**
     * @test
     */
    public function トラッキングUriを生成する()
    {
        $tracker = $this->getMock('ITEMAN_GATracker_Tracker',
                                  array('createHTTPRequest')
                                  );
        $tracker->expects($this->any())
                ->method('createHTTPRequest')
                ->will($this->returnValue($this->_request));

        $tracker->setWebPropertyID(self::$_webPropertyID);
        $tracker->trackPageView();

        $headers = $this->_request->getHeaders();

        $this->assertEquals(2, count($headers));
        $this->assertEquals($_SERVER['HTTP_USER_AGENT'], $headers['user-agent']);
        $this->assertEquals($_SERVER['HTTP_ACCEPT_LANGUAGE'], $headers['accepts-language']);

        $url = $this->_request->getUrl();

        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('www.google-analytics.com', $url->getHost());
        $this->assertEquals('/__utm.gif', $url->getPath());

        $queryVariables = $tracker->extractQueryVariables();

        $this->assertEquals(9, count(array_keys($queryVariables)));
        $this->assertEquals('4.4sh', $queryVariables['utmwv']);
        $this->assertGreaterThanOrEqual(0, $queryVariables['utmn']);
        $this->assertLessThanOrEqual(2147483647, $queryVariables['utmn']);
        $this->assertEquals('www.example.com', $queryVariables['utmhn']);
        $this->assertEquals('-', $queryVariables['utmr']);
        $this->assertEquals('/blog/', $queryVariables['utmp']);
        $this->assertEquals(self::$_webPropertyID, $queryVariables['utmac']);
        $this->assertEquals('__utma%3D999.999.999.999.999.1%3B', $queryVariables['utmcc']);
        $this->assertRegExp('/^0x.{16}$/', $queryVariables['utmvid']);
        $this->assertEquals('1.2.3.0', $queryVariables['utmip']);
    }

    /**
     * @test
     * @expectedException ITEMAN_GATracker_Exception
     */
    public function ウェブプロパティIdが与えられなかった場合例外を発生させる()
    {
        $tracker = new ITEMAN_GATracker_Tracker();
        $tracker->trackPageView();
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
