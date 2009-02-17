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

// {{{ ITEMAN_GAFilter_Converter_Mobile

/**
 * @package    ITEMAN_GAFilter
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GAFilter_Converter_Mobile implements ITEMAN_GAFilter_Converter_ConverterInterface
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    protected $backupGlobals = false;

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ convert()

    /**
     * @param ITEMAN_GAFilter_Tracker $tracker
     */
    public function convert(ITEMAN_GAFilter_Tracker $tracker)
    {
        $oldErrorReportingLevel = error_reporting(error_reporting() & ~E_STRICT);
        $oldFallbackOnNomatch =
            array_key_exists('NET_USERAGENT_MOBILE_FallbackOnNomatch', $GLOBALS) ? $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch']
                                                                                 : false;
        $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch'] = false;

        if (!Net_UserAgent_Mobile::isMobile($tracker->getUserAgent())) {
            PEAR::staticPopErrorHandling();
            $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch'] = $oldFallbackOnNomatch;
            error_reporting($oldErrorReportingLevel);
            return;
        }


        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        $mobile = Net_UserAgent_Mobile::factory($tracker->getUserAgent());
        if (Net_UserAgent_Mobile::isError($mobile)) {
            PEAR::staticPopErrorHandling();
            $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch'] = $oldFallbackOnNomatch;
            error_reporting($oldErrorReportingLevel);
            return;
        }

        $display = $mobile->getDisplay();
        $depth = $display->getDepth();
        if ($depth) {
            if ($depth == 16777216) {
                $tracker->setScreenColors('24-bit');
            } elseif ($depth == 262144) {
                $tracker->setScreenColors('18-bit');
            } elseif ($depth == 65536) {
                $tracker->setScreenColors('16-bit');
            } elseif ($depth == 4096) {
                $tracker->setScreenColors('12-bit');
            } elseif ($depth == 256) {
                $tracker->setScreenColors('8-bit');
            } elseif ($depth == 4) {
                $tracker->setScreenColors('2-bit');
            } elseif ($depth == 2) {
                $tracker->setScreenColors('1-bit');
            }
        }

        PEAR::staticPopErrorHandling();
        $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch'] = $oldFallbackOnNomatch;
        error_reporting($oldErrorReportingLevel);
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
