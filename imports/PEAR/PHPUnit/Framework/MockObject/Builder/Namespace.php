<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2010, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @subpackage Framework_MockObject_Builder
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://github.com/sebastianbergmann/phpunit-mock-objects
 * @since      File available since Release 1.0.0
 */

/**
 * Interface for builders which can register builders with a given identification.
 *
 * This interface relates to PHPUnit_Framework_MockObject_Builder_Identity.
 *
 * @package    PHPUnit
 * @subpackage Framework_MockObject_Builder
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 1.0.0beta2
 * @link       http://github.com/sebastianbergmann/phpunit-mock-objects
 * @since      Interface available since Release 1.0.0
 */
interface PHPUnit_Framework_MockObject_Builder_Namespace
{
    /**
     * Looks up the match builder with identification $id and returns it.
     *
     * @param string $id The identifiction of the match builder.
     * @return PHPUnit_Framework_MockObject_Builder_Match
     */
    public function lookupId($id);

    /**
     * Registers the match builder $builder with the identification $id. The
     * builder can later be looked up using lookupId() to figure out if it
     * has been invoked.
     *
     * @param string                                     $id
     *        The identification of the match builder.
     * @param PHPUnit_Framework_MockObject_Builder_Match $builder
     *        The builder which is being registered.
     */
    public function registerId($id, PHPUnit_Framework_MockObject_Builder_Match $builder);
}
