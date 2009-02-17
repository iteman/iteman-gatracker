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

// {{{ ITEMAN_GAFilter_Converter_MobileTest

/**
 * ITEMAN_GAFilter_Converter_Mobile のためのテスト。
 *
 * @package    ITEMAN_GAFilter
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GAFilter_Converter_MobileTest extends PHPUnit_Framework_TestCase
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
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';
        $_SERVER['REQUEST_URI'] = '/blog/';
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        $adapter = new HTTP_Request2_Adapter_Mock();
        $adapter->addResponse('HTTP/1.1 200 OK');
        $this->_request = new HTTP_Request2();
        $this->_request->setAdapter($adapter);
    }

    /**
     * @param string $userAgent
     * @param string $screenColors
     * @test
     * @dataProvider provideUserAgents
     */
    public function 画面の色を設定する($userAgent, $screenColors)
    {
        $tracker = $this->getMock('ITEMAN_GAFilter_Tracker',
                                  array('createHTTPRequest')
                                  );
        $tracker->expects($this->any())
                ->method('createHTTPRequest')
                ->will($this->returnValue($this->_request));

        $tracker->addConverter(new ITEMAN_GAFilter_Converter_Mobile());
        $tracker->setWebPropertyID(self::$_webPropertyID);
        $tracker->setUserAgent($userAgent);
        $tracker->trackPageView();

        $this->assertEquals($screenColors, $tracker->getScreenColors());
    }

    public function provideUserAgents()
    {
        return array(array('DoCoMo/2.0 SO905iCS(c100;TB;W24H18)', '24-bit'),
                     array('DoCoMo/2.0 F884i(c100;TJ)', '18-bit'),
                     array('DoCoMo/2.0 F801i(c100;TB;W24H17)', '16-bit'),
                     array('DoCoMo/1.0/F503iS/c10', '12-bit'),
                     array('DoCoMo/1.0/D502i', '8-bit'),
                     array('DoCoMo/1.0/N502i', '2-bit'),
                     array('DoCoMo/1.0/D501i', '1-bit')
                     );
    }

    /**
     * @test
     */
    public function 画面の解像度を設定する()
    {
        $tracker = $this->getMock('ITEMAN_GAFilter_Tracker',
                                  array('createHTTPRequest')
                                  );
        $tracker->expects($this->any())
                ->method('createHTTPRequest')
                ->will($this->returnValue($this->_request));

        $tracker->addConverter(new ITEMAN_GAFilter_Converter_Mobile());
        $tracker->setWebPropertyID(self::$_webPropertyID);
        $tracker->setUserAgent('DoCoMo/2.0 F884i(c100;TJ)');
        $tracker->trackPageView();

        $this->assertEquals('240x364', $tracker->getScreenResolution());
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
