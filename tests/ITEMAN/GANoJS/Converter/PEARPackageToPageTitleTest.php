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
 * @package    ITEMAN_GANoJS
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GANoJS_Converter_PEARPackageToPageTitleTest

/**
 * ITEMAN_GANoJS_Converter_PEARPackageToPageTitle のためのテスト。
 *
 * @package    ITEMAN_GANoJS
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GANoJS_Converter_PEARPackageToPageTitleTest extends PHPUnit_Framework_TestCase
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

    /**#@-*/

    /**#@+
     * @access public
     */

    public function setUp()
    {
        $_SERVER['ITEMAN_GANOJS_WEBPROPERTYID'] = 'UA-6415151-2';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; U; Linux i686; ja; rv:1.9.0.5) Gecko/2008121622 Ubuntu/8.10 (intrepid) Firefox/3.0.5';
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';

        $adapter = new HTTP_Request2_Adapter_Mock();
        $adapter->addResponse('HTTP/1.1 200 OK');
        $this->_request = new HTTP_Request2();
        $this->_request->setAdapter($adapter);
    }

    /**
     * @param string $uri
     * @param string $title
     * @test
     * @dataProvider providePEARPackages
     */
    public function Pearパッケージの場合ファイル名からタイトルを生成する($uri, $title)
    {
        $tracker = $this->getMock('ITEMAN_GANoJS_Tracker',
                                  array('createHTTPRequest',
                                        'getHostByAddr')
                                  );
        $tracker->expects($this->any())
                ->method('createHTTPRequest')
                ->will($this->returnValue($this->_request));
        $tracker->expects($this->any())
                ->method('getHostByAddr')
                ->will($this->returnValue('www.example.com'));

        $tracker->addConverter(new ITEMAN_GANoJS_Converter_PEARPackageToPageTitle());
        $tracker->setPage($uri);
        $tracker->trackPageView();

        $queryVariables = $tracker->extractQueryVariables();

        $this->assertEquals(rawurlencode($title), $queryVariables['utmdt']);
    }

    public function providePEARPackages()
    {
        return array(array('/get/Stagehand_TestRunner-2.6.1.tgz', 'Stagehand_TestRunner 2.6.1'),
                     array('/get/Stagehand_TestRunner-2.6.1.tar', 'Stagehand_TestRunner 2.6.1'),
                     array('/package/Net_UserAgent_Mobile-1.0.0RC1.tgz', 'Net_UserAgent_Mobile 1.0.0RC1'),
                     array('/Foo_Bar-0.1.0dev1.tgz', 'Foo_Bar 0.1.0dev1'),
                     array('/Foo_Bar-0.9.0alpha1.tgz', 'Foo_Bar 0.9.0alpha1'),
                     array('/Foo_Bar-0.9.0beta1.tgz', 'Foo_Bar 0.9.0beta1')
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