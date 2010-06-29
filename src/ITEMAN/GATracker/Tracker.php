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
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @link       http://www.ianlewis.org/jp/google-analytics
 * @link       http://code.google.com/p/gaforflash/
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GATracker_Tracker

/**
 * @package    ITEMAN_GATracker
 * @copyright  2008-2010 ITEMAN, Inc.
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
        $this->_initializeQueryVariables();
    }

    // }}}
    // {{{ trackPageView()

    /**
     */
    public function trackPageView()
    {
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
    // {{{ getHostname()

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->_queryVariables['utmhn'];
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
    // {{{ _buildRequest()

    /**
     */
    private function _buildRequest()
    {
        $this->_request = $this->createHTTPRequest();
        $this->_request->setUrl($this->_generateTrackingURI());
        $this->_request->setMethod(HTTP_Request2::METHOD_GET);
        $this->_request->setConfig(array('connect_timeout' => 10, 'timeout' => 30));
        $this->_request->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
        if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $this->_request->setHeader('Accepts-Language', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }
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
        $this->_queryVariables['utmwv'] = '4.4sh';
        $this->_queryVariables['utmn'] = $this->_generateRandomInteger();
        $this->_queryVariables['utmhn'] =
            array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : '';
        $this->_queryVariables['utmr'] =
            array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '-';
        $this->_queryVariables['utmp'] =
            array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '';
        $this->setWebPropertyID(null);
        $this->_queryVariables['utmcc'] = '__utma%3D999.999.999.999.999.1%3B';
        $this->_queryVariables['utmvid'] = $this->_generateVisitorId();
        $this->_queryVariables['utmip'] =
            array_key_exists('REMOTE_ADDR', $_SERVER) ? $this->_maskLastOctectOfIpAddress($_SERVER['REMOTE_ADDR'])
                                                      : '';
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
        return mt_rand(0, 0x7fffffff);
    }

    private function _generateVisitorId()
    {
        return '0x' . substr(md5($_SERVER['HTTP_USER_AGENT'] . uniqid(mt_rand(), true)), 0, 16);
    }

    private function _maskLastOctectOfIpAddress($ipAddress)
    {
        if (!preg_match('/^(\d+\.\d+\.\d+\.)\d+$/', $ipAddress, $matches)) {
            return '';
        }

        return $matches[1] . '0';
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
