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
 * @package    ITEMAN_GATracker
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @since      File available since Release 0.1.0
 */

// {{{ ITEMAN_GATracker_Converter_Mobile

/**
 * @package    ITEMAN_GATracker
 * @copyright  2009 ITEMAN, Inc.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ITEMAN_GATracker_Converter_Mobile implements ITEMAN_GATracker_Converter_ConverterInterface
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
     * @param ITEMAN_GATracker_Tracker $tracker
     */
    public function convert(ITEMAN_GATracker_Tracker $tracker)
    {
        $oldErrorReportingLevel = error_reporting(error_reporting() & ~E_STRICT);
        $oldFallbackOnNomatch =
            array_key_exists('NET_USERAGENT_MOBILE_FallbackOnNomatch', $GLOBALS) ? $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch']
                                                                                 : false;
        $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch'] = false;

        Stagehand_LegacyError_PEARError::enableConversion();
        try {
            if (!Net_UserAgent_Mobile::isMobile($tracker->getUserAgent())) {
                throw new ITEMAN_GATracker_Exception();
            }

            $mobile = Net_UserAgent_Mobile::factory($tracker->getUserAgent());

            $display = $mobile->getDisplay();
            $screenColors = $this->_getColorDepthByColors($display->getDepth());
            if (!is_null($screenColors)) {
                $tracker->setScreenColors($screenColors);
            }

            $width = $display->getWidth();
            $height = $display->getHeight();
            if ($width && $height) {
                $tracker->setScreenResolution("{$width}x{$height}");
            }
        } catch (Stagehand_LegacyError_PEARError_Exception $e) {
        } catch (ITEMAN_GATracker_Exception $e) {
        }
        Stagehand_LegacyError_PEARError::disableConversion();

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

    // }}}
    // {{{ _getColorDepthByColors()

    /**
     * @param integer $colors
     * @return string
     */
    private function _getColorDepthByColors($colors)
    {
        if ($colors == 16777216) {
            return '24-bit';
        } elseif ($colors == 262144) {
            return '18-bit';
        } elseif ($colors == 65536) {
            return '16-bit';
        } elseif ($colors == 4096) {
            return '12-bit';
        } elseif ($colors == 256) {
            return '8-bit';
        } elseif ($colors == 4) {
            return '2-bit';
        } elseif ($colors == 2) {
            return '1-bit';
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
