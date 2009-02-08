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
 * @version    SVN: $Id: AcceptLanguageTest.php 55 2009-02-08 14:04:10Z iteman $
 * @since      File available since Release 0.1.0
 */

// {{{ Stagehand_HTTP_AcceptLanguageTest

/**
 * Some tests for Stagehand_HTTP_AcceptLanguage.
 *
 * @package    Stagehand_HTTP_AcceptLanguage
 * @copyright  2009 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.1.0
 * @since      Class available since Release 0.1.0
 */
class Stagehand_HTTP_AcceptLanguageTest extends PHPUnit_Framework_TestCase
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

    public function provideDataForAcceptedLanguages()
    {
        return array(array(array('ja', 'en-us', 'en'), 'ja,en-us;q=0.7,en;q=0.3'),
                     array(array('da', 'en-gb', 'en'), 'da, en-gb;q=0.8, en;q=0.7'),
                     array(array('ja', 'en-us', 'en'), 'en-us;q=0.7,en;q=0.3,ja'),
                     array(array('ja', 'en-us', 'en'), 'en;q=0.3,en-us;q=0.7,ja'),
                     array(array('ja'), 'ja'),
                     array(array('ja', 'en'), 'ja, en'),
                     array(array('ja', 'en'), 'ja;q=0.7,en;q=0.7')
                     );
    }

    /**
     * @param array  $acceptedLanguages
     * @param string $acceptLanguage
     * @test
     * @dataProvider provideDataForAcceptedLanguages
     */
    public function provideAcceptedLanguagesSortedByQuality($acceptedLanguages, $acceptLanguage)
    {
        $this->assertEquals($acceptedLanguages,
                            Stagehand_HTTP_AcceptLanguage::getAcceptedLanguages($acceptLanguage)
                            );
    }

    /**
     * @param array  $acceptedLanguages
     * @param string $acceptLanguage
     * @test
     * @dataProvider provideDataForAcceptedLanguages
     */
    public function provideThePreferredLanguage($acceptedLanguages, $acceptLanguage)
    {
        $this->assertEquals(array_shift($acceptedLanguages),
                            Stagehand_HTTP_AcceptLanguage::getPreferredLanguage($acceptLanguage)
                            );
    }

    /**
     * @test
     */
    public function useHttpacceptlanguageIfTheArgumentIsNotGiven()
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja,en-us;q=0.7,en;q=0.3';

        $this->assertEquals(array('ja', 'en-us', 'en'),
                            Stagehand_HTTP_AcceptLanguage::getAcceptedLanguages()
                            );
        $this->assertEquals('ja',
                            Stagehand_HTTP_AcceptLanguage::getPreferredLanguage()
                            );
    }

    /**
     * @test
     */
    public function returnAnEmptyArrayFromGetacceptedlanguagesIfTheAcceptLanguageIsNullOrAnEmptyString()
    {
        $this->assertEquals(0,
                            count(Stagehand_HTTP_AcceptLanguage::getAcceptedLanguages())
                            );
        $this->assertEquals(0,
                            count(Stagehand_HTTP_AcceptLanguage::getAcceptedLanguages(null))
                            );
        $this->assertEquals(0,
                            count(Stagehand_HTTP_AcceptLanguage::getAcceptedLanguages(''))
                            );
    }

    /**
     * @test
     */
    public function returnNullFromGetpreferredlanguageIfTheAcceptLanguageIsNullOrAnEmptyString()
    {
        $this->assertNull(Stagehand_HTTP_AcceptLanguage::getPreferredLanguage());
        $this->assertNull(Stagehand_HTTP_AcceptLanguage::getPreferredLanguage(null));
        $this->assertNull(Stagehand_HTTP_AcceptLanguage::getPreferredLanguage(''));
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
