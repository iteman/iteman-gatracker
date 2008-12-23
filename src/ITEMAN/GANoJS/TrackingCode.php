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
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GANoJS_TrackingCode

/**
 * @package    ITEMAN_GANoJS
 * @copyright  2008 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://www.vdgraaf.info/google-analytics-without-javascript.html
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GANoJS_TrackingCode
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
    private $_gaURI;
    private $_cookieA;
    private $_cookieZ;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ __construct()

    /**
     * @param string  $webPropertyID
     * @param boolean $useSSL
     */
    public function __construct($webPropertyID, $useSSL = false)
    {
        $this->_queryVariables = array('utmwv'  => '4.3',
                                       'utmn'   => mt_rand(0, 2147483647),
                                       'utmhn'  => null,
                                       'utmcs'  => null,
                                       'utmsr'  => null,
                                       'utmsc'  => null,
                                       'utmje'  => null,
                                       'utmfl'  => null,
                                       'utmdt'  => null,
                                       'utmhid' => mt_rand(0, 2147483647),
                                       'utmr'   => '-',
                                       'utmp'   => null,
                                       'utmac'  => $webPropertyID,
                                       'utmcc'  => array($this, 'generateCookieConfiguration')
                                       );

        $this->_gaURI = !$useSSL ? 'http://www.google-analytics.com/__utm.gif'
                                 : 'https://ssl.google-analytics.com/__utm.gif';
    }

    // }}}
    // {{{ setGAVersion()

    /**
     * @param string $gaVersion
     */
    public function setGAVersion($gaVersion)
    {
        $this->_queryVariables['utmwv'] = $gaVersion;
    }

    // }}}
    // {{{ setHost()

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->_queryVariables['utmhn'] = $host;
    }

    // }}}
    // {{{ setDocumentEncoding()

    /**
     * @param string $documentEncoding
     */
    public function setDocumentEncoding($documentEncoding)
    {
        $this->_queryVariables['utmcs'] = $documentEncoding;
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
    // {{{ setScreenColor()

    /**
     * @param string $screenColor
     */
    public function setScreenColor($screenColor)
    {
        $this->_queryVariables['utmsc'] = $screenColor;
    }

    // }}}
    // {{{ setUserLanguage()

    /**
     * @param string $userLanguage
     */
    public function setUserLanguage($userLanguage)
    {
        $this->_queryVariables['utmul'] = $userLanguage;
    }

    // }}}
    // {{{ setJavaEnabled()

    /**
     * @param boolean $javaEnabled
     */
    public function setJavaEnabled($javaEnabled)
    {
        $this->_queryVariables['utmje'] = $javaEnabled ? '1' : '0';
    }

    // }}}
    // {{{ setFlashVersion()

    /**
     * @param string $flashVersion
     */
    public function setFlashVersion($flashVersion)
    {
        $this->_queryVariables['utmfl'] = rawurlencode($flashVersion);
    }

    // }}}
    // {{{ setDocumentTitle()

    /**
     * @param string $documentTitle
     */
    public function setDocumentTitle($documentTitle)
    {
        $this->_queryVariables['utmdt'] = rawurlencode($documentTitle);
    }

    // }}}
    // {{{ setDocument()

    /**
     * @param string $document
     */
    public function setDocument($document)
    {
        $this->_queryVariables['utmp'] = $document;
    }

    // }}}
    // {{{ setCookieA()

    /**
     * @param string $cookieA
     */
    public function setCookieA($cookieA)
    {
        $this->_cookieA = $cookieA;
    }

    // }}}
    // {{{ setCookieZ()

    /**
     * @param string $cookieZ
     */
    public function setCookieZ($cookieZ)
    {
        $this->_cookieZ = $cookieZ;
    }

    // }}}
    // {{{ generateCookieConfiguration()

    /**
     * @return string
     */
    public function generateCookieConfiguration()
    {
        return strtr(rawurlencode("__utma={$this->_cookieA};+__utmz={$this->_cookieZ};"),
                     array('%28' => '(', '%29' => ')')
                     );
    }

    // }}}
    // {{{ generateTrackingURI()

    /**
     * @return string
     */
    public function generateTrackingURI()
    {
        $queryVariables = array();
        foreach ($this->_queryVariables as $name => $value) {
            if (!is_callable($value)) {
                $queryVariables[$name] = $value;
            } else {
                $queryVariables[$name] = call_user_func($value);
            }
        }

        $url = new Net_URL2($this->_gaURI);
        $url->setQueryVariables($queryVariables);
        return $url->getURL();
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
