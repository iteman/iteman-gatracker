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

// {{{ ITEMAN_GANoJS_TrackingCodeTest

/**
 * Some tests for ITEMAN_GANoJS_TrackingCode.
 *
 * @package    ITEMAN_GANoJS
 * @copyright  2008 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GANoJS_TrackingCodeTest extends PHPUnit_Framework_TestCase
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
    public function generateTrackingUri()
    {
        $trackingCode = new ITEMAN_GANoJS_TrackingCode('UA-6415151-2');
        $trackingCode->setGAVersion('4.3');
        $trackingCode->setHost('iteman.jp');
        $trackingCode->setDocumentEncoding('UTF-8');
        $trackingCode->setScreenResolution('1024x768');
        $trackingCode->setScreenColor('24-bit');
        $trackingCode->setUserLanguage('ja');
        $trackingCode->setJavaEnabled(false);
        $trackingCode->setFlashVersion('9.0 r152');
        $trackingCode->setDocumentTitle('ITEMAN Blog - アイテマンブログ');
        $trackingCode->setDocument('/blog/');
        $trackingCode->setCookieA('269003561.3095504869349727700.1229619879.1229923372.1229940603.8');
        $trackingCode->setCookieZ('269003561.1229781229.4.4.utmcsr=mt.iteman.jp|utmccn=(referral)|utmcmd=referral|utmcct=/mt.cgi');
        $trackingURI = $trackingCode->generateTrackingURI();
        $uriElements = parse_url($trackingURI);

        $this->assertEquals('http', $uriElements['scheme']);
        $this->assertEquals('www.google-analytics.com', $uriElements['host']);
        $this->assertEquals('/__utm.gif', $uriElements['path']);

        $queryVariables = array();
        foreach (explode('&', $uriElements['query']) as $queryVariable) {
            list($name, $value) = explode('=', $queryVariable);
            $queryVariables[$name] = $value;
        }

        $this->assertEquals('4.3', $queryVariables['utmwv']);
        $this->assertGreaterThanOrEqual(0, $queryVariables['utmn']);
        $this->assertLessThanOrEqual(2147483647, $queryVariables['utmn']);
        $this->assertLessThanOrEqual('iteman.jp', $queryVariables['utmhn']);
        $this->assertEquals('UTF-8', $queryVariables['utmcs']);
        $this->assertEquals('1024x768', $queryVariables['utmsr']);
        $this->assertEquals('24-bit', $queryVariables['utmsc']);
        $this->assertEquals('ja', $queryVariables['utmul']);
        $this->assertEquals('0', $queryVariables['utmje']);
        $this->assertEquals(rawurlencode('9.0 r152'), $queryVariables['utmfl']);
        $this->assertEquals(rawurlencode('ITEMAN Blog - アイテマンブログ'),
                            $queryVariables['utmdt']
                            );
        $this->assertGreaterThanOrEqual(0, $queryVariables['utmhid']);
        $this->assertLessThanOrEqual(2147483647, $queryVariables['utmhid']);
        $this->assertEquals('-', $queryVariables['utmr']);
        $this->assertEquals('/blog/', $queryVariables['utmp']);
        $this->assertEquals('UA-6415151-2', $queryVariables['utmac']);
        $this->assertEquals('__utma%3D269003561.3095504869349727700.1229619879.1229923372.1229940603.8%3B%2B__utmz%3D269003561.1229781229.4.4.utmcsr%3Dmt.iteman.jp%7Cutmccn%3D(referral)%7Cutmcmd%3Dreferral%7Cutmcct%3D%2Fmt.cgi%3B', $queryVariables['utmcc']);

/* o http://www.google-analytics.com/__utm.gif */
/* o utmwv=4.3 */
/* o utmn=1327950861 */
/* o utmhn=iteman.jp */
/* o utmcs=UTF-8 */
/* o utmsr=1024x768 */
/* o utmsc=24-bit */
/* o utmul=ja */
/* o utmje=0 */
/* o utmfl=9.0%20r152 */
/* o utmdt=ITEMAN%20Blog%20-%20%E3%82%A2%E3%82%A4%E3%83%86%E3%83%9E%E3%83%B3%E3%83%96%E3%83%AD%E3%82%B0 */
/* o utmhid=1642844647 */
/* o utmr=- */
/* o utmp=/blog/ */
/* o utmac=UA-6415151-2 */
/* utmcc=__utma%3D269003561.3095504869349727700.1229619879.1229785742.1229864886.6%3B%2B__utmz%3D269003561.1229781229.4.4.utmcsr%3Dmt.iteman.jp%7Cutmccn%3D(referral)%7Cutmcmd%3Dreferral%7Cutmcct%3D%2Fmt.cgi%3B */
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
