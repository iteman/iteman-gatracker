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
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @link       http://www.ianlewis.org/jp/google-analytics
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GANoJS_Tracker

/**
 * @package    ITEMAN_GANoJS
 * @copyright  2008 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @link       http://www.ianlewis.org/jp/google-analytics
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GANoJS_Tracker
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

    private $_queryVariables = array();
    private $_cookieA;
    private $_cookieZ;
    private $_userAgent;
    private $_acceptLanguage;

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
        $this->_queryVariables = array('utmwv'  => '4.3',
                                       'utmn'   => mt_rand(1000000000, 9999999999),
                                       'utmcs'  => 'UTF-8',
                                       'utmsr'  => '-',
                                       'utmsc'  => '-',
                                       'utmul'  => '-',
                                       'utmje'  => '0',
                                       'utmfl'  => '-',
                                       'utmdt'  => '-',
                                       'utmhn'  => @$_SERVER['SERVER_NAME'],
                                       'utmr'   => array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '-',
                                       'utmp'   => @$_SERVER['REQUEST_URI'],
                                       'utmac'  => @$_SERVER['ITEMAN_GANOJS_WEBPROPERTYID'],
                                       'utmhid' => mt_rand(0, 2147483647),
                                       'utmcc'  => array($this, 'generateCookieConfiguration')
                                       );

        $this->_userAgent = @$_SERVER['HTTP_USER_AGENT'];
        $this->_acceptLanguage = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    // }}}
    // {{{ generateCookieConfiguration()

    /**
     * @return string
     */
    public function generateCookieConfiguration()
    {
        $cookieNumber = mt_rand(0, 2147483647);
        $currentTimestamp = time();
        return strtr(rawurlencode(sprintf('__utma=%d.%d.%d.%d.%d.2;+__utmb=%d;+__utmc=%d;+__utmz=%d.%d.2.2.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none);',
                       $cookieNumber, mt_rand(1000000000, 2147483647), $currentTimestamp, $currentTimestamp, $currentTimestamp, // __utma
                       $cookieNumber, // __utmb
                       $cookieNumber, // __utmc
                       $cookieNumber, $currentTimestamp // __utmz
                                          )),
                     array('%28' => '(', '%29' => ')')
                     );
    }

    // }}}
    // {{{ trackPageView()

    /**
     * @throws ITEMAN_GANoJS_Exception
     */
    public function trackPageView()
    {
        $this->_validate();
        $request = $this->createHTTPRequest();
        $request->setUrl($this->_generateTrackingURI());
        $request->setMethod(HTTP_Request2::METHOD_GET);
        $request->setConfig(array('connect_timeout' => 10, 'timeout' => 30));
        $request->setHeader('User-Agent', $this->_userAgent);
        if (!is_null($this->_acceptLanguage)) {
            $request->setHeader('Accept-Language', $this->_acceptLanguage);
        }

        $response = $request->send();
        if ($response->getStatus() != '200') {
            throw new ITEMAN_GANoJS_Exception('200 以外のステータスコードが返されました');
        }
    }

    // }}}
    // {{{ createHTTPRequest()

    /**
     * @return HTTP_Request2
     */
    public function createHTTPRequest()
    {
        return new HTTP_Request2();
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _validate()

    /**
     */
    private function _validate()
    {
        foreach (array('utmhn', 'utmp', 'utmac') as $requiredVariable) {
            if (is_null($this->_queryVariables[$requiredVariable])) {
                throw new ITEMAN_GANoJS_Exception("クエリ変数 [ $requiredVariable ] は必須です");
            }
        }

        if (is_null($this->_userAgent)) {
            throw new ITEMAN_GANoJS_Exception('ユーザエージェントは必須です');
        }
    }

    // }}}
    // {{{ _generateTrackingURI()

    /**
     * @return string
     * @throws ITEMAN_GANoJS_Exception
     */
    private function _generateTrackingURI()
    {
        $queryVariables = array();
        foreach ($this->_queryVariables as $name => $value) {
            if (!is_callable($value)) {
                $queryVariables[$name] = $value;
            } else {
                $queryVariables[$name] = call_user_func($value);
            }
        }

        $url = new Net_URL2('http://www.google-analytics.com/__utm.gif');
        $url->setQueryVariables($queryVariables);
        return $url->getURL();
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
