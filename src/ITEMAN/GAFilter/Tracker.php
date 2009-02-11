<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2008-2009 ITEMAN, Inc. All rights reserved.
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
 * @copyright  2008-2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @link       http://www.ianlewis.org/jp/google-analytics
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GAFilter_Tracker

/**
 * @package    ITEMAN_GAFilter
 * @copyright  2008-2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @link       http://www.ianlewis.org/jp/google-analytics
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GAFilter_Tracker
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
    private $_converters = array();
    private $_request;

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
                                       'utmje'  => '0',
                                       'utmfl'  => '-',
                                       'utmhid' => mt_rand(0, 2147483647),
                                       'utmcc'  => array($this, 'generateCookieConfiguration')
                                       );
        $this->setWebPropertyID(null);
        $this->setHostname(null);
        $this->setPage(null);
        $this->setSource('-');
        $this->setPageTitle('-');
        $this->setLanguage('-');

        $this->addConverter(new ITEMAN_GAFilter_Converter_ServerNameToHostname());
        $this->addConverter(new ITEMAN_GAFilter_Converter_RequestURIToPage());
        $this->addConverter(new ITEMAN_GAFilter_Converter_UserAgent());
        $this->addConverter(new ITEMAN_GAFilter_Converter_RefererToSource());
        $this->addConverter(new ITEMAN_GAFilter_Converter_AcceptLanguageToLanguage());
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
     */
    public function trackPageView()
    {
        $this->_convert();
        $this->_validate();
        $this->_buildRequest();
        $this->_sendRequest();
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

    // }}}
    // {{{ setPageTitle()

    /**
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->_queryVariables['utmdt'] = rawurlencode($pageTitle);
    }

    // }}}
    // {{{ addConverter()

    /**
     * @param ITEMAN_GAFilter_Converter_ConverterInterface $converter
     */
    public function addConverter(ITEMAN_GAFilter_Converter_ConverterInterface $converter)
    {
        $this->_converters[] = $converter;
    }

    // }}}
    // {{{ extractQueryVariables()

    /**
     * @return array
     */
    public function extractQueryVariables()
    {
        if (is_null($this->_request)) {
            return array();
        }

        $queryVariables = array();
        foreach (explode('&', $this->_request->getUrl()->getQuery()) as $queryVariable) {
            list($name, $value) = explode('=', $queryVariable);
            $queryVariables[$name] = $value;
        }

        return $queryVariables;
    }

    // }}}
    // {{{ setPage()

    /**
     * @paran string $page
     */
    public function setPage($page)
    {
        $this->_queryVariables['utmp'] = $page;
    }

    // }}}
    // {{{ getPage()

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->_queryVariables['utmp'];
    }

    // }}}
    // {{{ setHostname()

    /**
     * @paran string $hostname
     */
    public function setHostname($hostname)
    {
        $this->_queryVariables['utmhn'] = rawurlencode($hostname);
    }

    // }}}
    // {{{ getHostname()

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->_queryVariables['utmhn'];
    }

    // }}}
    // {{{ getPageTitle()

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->_queryVariables['utmdt'];
    }

    // }}}
    // {{{ getSource()

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->_queryVariables['utmr'];
    }

    // }}}
    // {{{ setSource()

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->_queryVariables['utmr'] = $source;
    }

    // }}}
    // {{{ setUserAgent()

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
    }

    // }}}
    // {{{ setWebPropertyID()

    /**
     * @param string $webPropertyID
     */
    public function setWebPropertyID($webPropertyID)
    {
        $this->_queryVariables['utmac'] = $webPropertyID;
    }

    // }}}
    // {{{ getWebPropertyID()

    /**
     * @return string
     */
    public function getWebPropertyID()
    {
        return $this->_queryVariables['utmac'];
    }

    // }}}
    // {{{ setLanguage()

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->_queryVariables['utmul'] = $language;
    }

    // }}}
    // {{{ getLanguage()

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->_queryVariables['utmul'];
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
        if (is_null($this->getWebPropertyID())) {
            throw new ITEMAN_GAFilter_Exception('ウェブプロパティIDが設定されていません');
        }

        if (is_null($this->getPage())) {
            throw new ITEMAN_GAFilter_Exception('ページが設定されていません');
        }

        if (is_null($this->getHostname())) {
            throw new ITEMAN_GAFilter_Exception('ホスト名が設定されていません');
        }

        if (is_null($this->_userAgent)) {
            throw new ITEMAN_GAFilter_Exception('ユーザエージェントが設定されていません');
        }
    }

    // }}}
    // {{{ _generateTrackingURI()

    /**
     * @return string
     * @throws ITEMAN_GAFilter_Exception
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

    // }}}
    // {{{ _convert()

    /**
     */
    private function _convert()
    {
        foreach ($this->_converters as $converter) {
            $converter->convert($this);
        }
    }

    // }}}
    // {{{ _buildRequest()

    /**
     */
    private function _buildRequest()
    {
        $this->_request = $this->createHTTPRequest();
        $this->_request->setUrl($this->_generateTrackingURI());
        $this->_request->setMethod(HTTP_Request2::METHOD_GET);
        $this->_request->setConfig(array('connect_timeout' => 10, 'timeout' => 30));
        $this->_request->setHeader('User-Agent', $this->_userAgent);
    }

    // }}}
    // {{{ _sendRequest()

    /**
     * @throws ITEMAN_GAFilter_Exception
     */
    private function _sendRequest()
    {
        $response = $this->_request->send();
        if ($response->getStatus() != '200') {
            throw new ITEMAN_GAFilter_Exception('200 以外のステータスコードが返されました');
        }
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
