<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2009 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
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
 * @package    Stagehand_HTTP_AcceptLanguage
 * @copyright  2009 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: AcceptLanguage.php 55 2009-02-08 14:04:10Z iteman $
 * @link       http://mashing-it-up.blogspot.com/2008/10/parsing-accept-language-in-rails.html
 * @link       http://docs.komagata.org/679
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
 * @since      File available since Release 0.1.0
 */

// {{{ Stagehand_HTTP_AcceptLanguage

/**
 * A utility class for the Accept-Language request-header field.
 *
 * @package    Stagehand_HTTP_AcceptLanguage
 * @copyright  2009 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.1.0
 * @link       http://mashing-it-up.blogspot.com/2008/10/parsing-accept-language-in-rails.html
 * @link       http://docs.komagata.org/679
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
 * @since      Class available since Release 0.1.0
 */
class Stagehand_HTTP_AcceptLanguage
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

    // }}}
    // {{{ getAcceptedLanguages()

    /**
     * Returns the languages accepted by the clients, sorted by quality.
     *
     * @param string $acceptLanguage
     * @return array
     */
    public static function getAcceptedLanguages($acceptLanguage = null)
    {
        if (is_null($acceptLanguage)) {
            $acceptLanguage = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }

        if (is_null($acceptLanguage) || !strlen($acceptLanguage)) {
            return array();
        }

        $acceptedLanguages = array();
        $i = 0;
        foreach (explode(',', str_replace(' ', '', $acceptLanguage)) as $acceptedLanguage) {
            if (strpos($acceptedLanguage, ';q=') !== false) {
                list($languageRange, $qvalue) = explode(';q=', $acceptedLanguage);
            } else {
                $languageRange = $acceptedLanguage;
                $qvalue = 1;
            }

            $acceptedLanguages[$languageRange] = array('qvalue' => $qvalue,
                                                       'originalOrder' => $i
                                                       );
            ++$i;
        }

        uasort($acceptedLanguages, array(__CLASS__, 'compareAcceptedLanguages'));
        return array_keys($acceptedLanguages);
    }

    // }}}
    // {{{ compareAcceptedLanguages()

    /**
     * Compares two elements of accepted languages.
     *
     * @param array $a
     * @param array $b
     * @return integer
     */
    public static function compareAcceptedLanguages(array $a, array $b)
    {
        if ($a['qvalue'] == $b['qvalue']) {
            return $a['originalOrder'] > $a['originalOrder'] ? -1 : 1;
        }

        return $a['qvalue'] > $b['qvalue'] ? -1 : 1;
    }

    // }}}
    // {{{ getPreferredLanguage()

    /**
     * Returns the language preferred by the client.
     *
     * @param string $acceptLanguage
     * @return string
     */
    public static function getPreferredLanguage($acceptLanguage = null)
    {
        $acceptedLanguages = self::getAcceptedLanguages($acceptLanguage);
        if (!count($acceptedLanguages)) {
            return;
        }

        return $acceptedLanguages[0];
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
