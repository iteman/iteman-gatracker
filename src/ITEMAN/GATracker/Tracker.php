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
 * @package    ITEMAN_GATracker
 * @copyright  2008-2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @link       http://www.ianlewis.org/jp/google-analytics
 * @link       http://code.google.com/p/gaforflash/
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GATracker_Tracker

/**
 * @package    ITEMAN_GATracker
 * @copyright  2008-2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @link       http://www.ianlewis.org/jp/google-analytics
 * @link       http://code.google.com/p/gaforflash/
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GATracker_Tracker
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
    private $_userAgent;
    private $_converters = array();
    private $_request;
    private $_domainHash;
    private $_sessionID;
    private $_firstVisitTime;
    private $_lastVisitTime;
    private $_sessionCount = 0;

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
        $this->_initializeQueryVariables();
        $this->_configureConverters();
    }

    // }}}
    // {{{ generateCookieConfiguration()

    /**
     * @return string
     */
    public function generateCookieConfiguration()
    {
        $this->_domainHash = $this->_generateHash(@$_SERVER['SERVER_NAME']);
        $this->_generateVisitorTrackingCookie();
        return strtr(rawurlencode(implode('+', array('__utma=' . $this->_generateVisitorTrackingCookie() . ';',
                                                     '__utmz=' . $this->_generateCampaignTrackingCookie() . ';'))),
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
     * @param ITEMAN_GATracker_Converter_ConverterInterface $converter
     */
    public function addConverter(ITEMAN_GATracker_Converter_ConverterInterface $converter)
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

    // }}}
    // {{{ getUserAgent()

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    // }}}
    // {{{ setScreenColors()

    /**
     * @param string $screenColors
     */
    public function setScreenColors($screenColors)
    {
        $this->_queryVariables['utmsc'] = $screenColors;
    }

    // }}}
    // {{{ getScreenColors()

    /**
     * @return string
     */
    public function getScreenColors()
    {
        return $this->_queryVariables['utmsc'];
    }

    // }}}
    // {{{ setScreenResolution()

    /**
     * @param string $screenResolution
     */
    public function setScreenResolution($screenResolution)
    {
        $this->_queryVariables['utmsr'] = $screenResolution;
    }

    // }}}
    // {{{ getScreenResolution()

    /**
     * @return string
     */
    public function getScreenResolution()
    {
        return $this->_queryVariables['utmsr'];
    }

    // }}}
    // {{{ setSessionID()

    /**
     * @param string $sessionID
     */
    public function setSessionID($sessionID)
    {
        $this->_sessionID = $this->_generateHash($sessionID);
    }

    // }}}
    // {{{ getSessionID()

    /**
     * @return string
     */
    public function getSessionID()
    {
        return $this->_sessionID;
    }

    // }}}
    // {{{ setFirstVisitTime()

    /**
     * @param integer $firstVisitTime
     */
    public function setFirstVisitTime($firstVisitTime)
    {
        $this->_firstVisitTime = $firstVisitTime;
    }

    // }}}
    // {{{ getFirstVisitTime()

    /**
     * @return integer
     */
    public function getFirstVisitTime()
    {
        return $this->_firstVisitTime;
    }

    // }}}
    // {{{ setLastVisitTime()

    /**
     * @param integer $lastVisitTime
     */
    public function setLastVisitTime($lastVisitTime)
    {
        $this->_lastVisitTime = $lastVisitTime;
    }

    // }}}
    // {{{ getLastVisitTime()

    /**
     * @return integer
     */
    public function getLastVisitTime()
    {
        return $this->_lastVisitTime;
    }

    // }}}
    // {{{ setSessionCount()

    /**
     * @param integer $sessionCount
     */
    public function setSessionCount($sessionCount)
    {
        $this->_sessionCount = $sessionCount;
    }

    // }}}
    // {{{ getSessionCount()

    /**
     * @return integer
     */
    public function getSessionCount()
    {
        return $this->_sessionCount;
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
            throw new ITEMAN_GATracker_Exception('ウェブプロパティIDが設定されていません');
        }

        if (is_null($this->getPage())) {
            throw new ITEMAN_GATracker_Exception('ページが設定されていません');
        }

        if (is_null($this->getHostname())) {
            throw new ITEMAN_GATracker_Exception('ホスト名が設定されていません');
        }

        if (is_null($this->_userAgent)) {
            throw new ITEMAN_GATracker_Exception('ユーザエージェントが設定されていません');
        }
    }

    // }}}
    // {{{ _generateTrackingURI()

    /**
     * @return string
     * @throws ITEMAN_GATracker_Exception
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
     * @throws ITEMAN_GATracker_Exception
     */
    private function _sendRequest()
    {
        $response = $this->_request->send();
        if ($response->getStatus() != '200') {
            throw new ITEMAN_GATracker_Exception('200 以外のステータスコードが返されました');
        }
    }

    // }}}
    // {{{ _initializeQueryVariables()

    /**
     */
    private function _initializeQueryVariables()
    {
        $this->_queryVariables['utmwv'] = '4.3';
        $this->_queryVariables['utmn'] = $this->_generateRandomInteger();
        $this->setHostname(null);
        $this->_queryVariables['utmcs'] = 'UTF-8';
        $this->setScreenResolution('-');
        $this->setScreenColors('-');
        $this->setLanguage('-');
        $this->_queryVariables['utmje'] = '0';
        $this->_queryVariables['utmfl'] = '-';
        $this->setPageTitle('-');
        $this->_queryVariables['utmhid'] = $this->_generateRandomInteger();
        $this->setSource('-');
        $this->setPage(null);
        $this->setWebPropertyID(null);
        $this->_queryVariables['utmcc'] = array($this, 'generateCookieConfiguration');
    }

    // }}}
    // {{{ _configureConverters()

    /**
     */
    private function _configureConverters()
    {
        $this->addConverter(new ITEMAN_GATracker_Converter_ServerNameToHostname());
        $this->addConverter(new ITEMAN_GATracker_Converter_RequestURIToPage());
        $this->addConverter(new ITEMAN_GATracker_Converter_UserAgent());
        $this->addConverter(new ITEMAN_GATracker_Converter_RefererToSource());
        $this->addConverter(new ITEMAN_GATracker_Converter_AcceptLanguageToLanguage());
    }

    // }}}
    // {{{ _generateRandomInteger()

    /**
     * ランダムな正の整数 (32 ビット) を生成する。
     *
     * @return integer
     */
    private function _generateRandomInteger()
    {
        return mt_rand(0, 2147483647);
    }

    // }}}
    // {{{ _generateHash()

    /**
     * 与えられた文字列に対応するハッシュを生成する。
     * このメソッドは com.google.analytics.core.Utils.generateHash() の移植である。
     *
     * @param string $input
     * @return integer
     * @link http://code.google.com/p/gaforflash/source/browse/trunk/src/com/google/analytics/core/Utils.as
     */
    private function _generateHash($input)
    {
        $hash = 1;
        $leftMost7 = 0;

        for ($position = strlen($input) - 1; $position >= 0; --$position) {
            $current = ord(substr($input, $position, 1));
            $hash = (($hash << 6) & 0xfffffff) + $current + ($current << 14);
            $leftMost7 = $hash & 0xfe00000;

            if ($leftMost7 != 0) {
                $hash ^= $leftMost7 >> 21;
            }
        }

        return $hash;
    }

    // }}}
    // {{{ _generateVisitorTrackingCookie()

    /**
     * ユーザトラッキング Cookie (utma) を生成する。
     *
     * @return string
     */
    private function _generateVisitorTrackingCookie()
    {
        $sessionID = $this->getSessionID();
        if (is_null($sessionID)) {
            $this->setSessionID(uniqid(mt_rand(), true));
            $sessionID = $this->getSessionID();
        }

        $firstVisitTime = $this->getFirstVisitTime();
        $lastVisitTime = $this->getLastVisitTime();
        $currentTime = time();

        if (!(!is_null($firstVisitTime) && !is_null($lastVisitTime)
              && $firstVisitTime <= $lastVisitTime
              && $lastVisitTime <= $currentTime)
            ) {
            $this->setFirstVisitTime($currentTime);
            $this->setLastVisitTime($currentTime);
            $firstVisitTime = $this->getFirstVisitTime();
            $lastVisitTime = $this->getLastVisitTime();
        }

        $sessionCount = $this->getSessionCount();
        $sessionCount += 1;

        return "{$this->_domainHash}.$sessionID.$firstVisitTime.$lastVisitTime.$currentTime.$sessionCount";
    }

    // }}}
    // {{{ _generateCampaignTrackingCookie()

    /**
     * キャンペーントラッキング Cookie (utmz) を生成する。
     *
     * @return string
     */
    private function _generateCampaignTrackingCookie()
    {
        $creation = time();
        $sessions = 1;
        $responseCount = 1;
        $name = '(direct)';
        $clickSource = '(direct)';
        $deliveryMethod = '(none)';

        return "{$this->_domainHash}.$creation.$sessions.$responseCount.utmccn=$name|utmcsr=$clickSource|utmcmd=$deliveryMethod";
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
