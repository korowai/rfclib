<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\TestsNocov\Lib\Rfc;

use Korowai\Lib\Rfc\Rfc5234;
use Korowai\Testing\Rfclib\TestCase;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Rfc5234Test extends TestCase
{
    public static function getRfcClass() : string
    {
        return Rfc5234::class;
    }

    public function test__constValues()
    {
        // character lists for character classes
        $this->assertSame('A-Za-z', Rfc5234::ALPHACHARS);
        $this->assertSame('01', Rfc5234::BITCHARS);
        $this->assertSame('\x01-\x7F', Rfc5234::CHARCHARS);
        $this->assertSame('\r', Rfc5234::CRCHARS);
        $this->assertSame('\x00-\x1F\x7F', Rfc5234::CTLCHARS);
        $this->assertSame('0-9', Rfc5234::DIGITCHARS);
        $this->assertSame('0-9A-F', Rfc5234::HEXDIGCHARS);
        $this->assertSame('\t', Rfc5234::HTABCHARS);
        $this->assertSame('\n', Rfc5234::LFCHARS);
        $this->assertSame('\x00-\xFF', Rfc5234::OCTETCHARS);
        $this->assertSame(' ', Rfc5234::SPCHARS);
        $this->assertSame('\x21-\x7E', Rfc5234::VCHARCHARS);
        $this->assertSame(' \t', Rfc5234::WSPCHARS);

        // Core rules
        $this->assertSame('[A-Za-z]', Rfc5234::ALPHA);
        $this->assertSame('[01]', Rfc5234::BIT);
        $this->assertSame('[\x01-\x7F]', Rfc5234::CHAR);
        $this->assertSame('\r', Rfc5234::CR);
        $this->assertSame('(?:\r\n)', Rfc5234::CRLF);
        $this->assertSame('[\x00-\x1F\x7F]', Rfc5234::CTL);
        $this->assertSame('[0-9]', Rfc5234::DIGIT);
        $this->assertSame('"', Rfc5234::DQUOTE);
        $this->assertSame('[0-9A-F]', Rfc5234::HEXDIG);
        $this->assertSame('\t', Rfc5234::HTAB);
        $this->assertSame('\n', Rfc5234::LF);
        $this->assertSame('(?:(?:[ \t]|(?:\r\n)[ \t])*)', Rfc5234::LWSP);
        $this->assertSame('[\x00-\xFF]', Rfc5234::OCTET);
        $this->assertSame(' ', Rfc5234::SP);
        $this->assertSame('[\x21-\x7E]', Rfc5234::VCHAR);
        $this->assertSame('[ \t]', Rfc5234::WSP);
    }
}

// vim: syntax=php sw=4 ts=4 et:
