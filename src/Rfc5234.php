<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Rfc;

/**
 * Resulable PCRE expressions for core rules defined in
 * [RFC5234](https://tools.ietf.org/html/rfc5234#appendix-B.1).
 */
class Rfc5234 extends AbstractRuleSet
{
    // character lists for character classes
    public const ALPHACHARS = 'A-Za-z';
    public const BITCHARS = '01';
    public const CHARCHARS = '\x01-\x7F';
    public const CRCHARS = '\r';
    public const CTLCHARS = '\x00-\x1F\x7F';
    public const DIGITCHARS = '0-9';
    public const HEXDIGCHARS = self::DIGITCHARS.'A-F';
    public const HTABCHARS = '\t';
    public const LFCHARS = '\n';
    public const OCTETCHARS = '\x00-\xFF';
    public const SPCHARS = ' ';
    public const VCHARCHARS = '\x21-\x7E';
    public const WSPCHARS = ' \t';

    // [core rules](https://tools.ietf.org/html/rfc5234#appendix-B.1)
    public const ALPHA = '['.self::ALPHACHARS.']';
    public const BIT = '['.self::BITCHARS.']';
    public const CHAR = '['.self::CHARCHARS.']';
    public const CR = '\r';
    public const CRLF = '(?:\r\n)';
    public const CTL = '['.self::CTLCHARS.']';
    public const DIGIT = '['.self::DIGITCHARS.']';
    public const DQUOTE = '"';
    public const HEXDIG = '['.self::HEXDIGCHARS.']';
    public const HTAB = '\t';
    public const LF = '\n';
    public const LWSP = '(?:(?:['.self::WSPCHARS.']|'.self::CRLF.'['.self::WSPCHARS.'])*)';
    public const OCTET = '['.self::OCTETCHARS.']';
    public const SP = ' ';
    public const VCHAR = '['.self::VCHARCHARS.']';
    public const WSP = '['.self::WSPCHARS.']';

    protected static $rfc5234Rules = [
        'ALPHACHARS',
        'BITCHARS',
        'CHARCHARS',
        'CRCHARS',
        'CTLCHARS',
        'DIGITCHARS',
        'HEXDIGCHARS',
        'HTABCHARS',
        'LFCHARS',
        'OCTETCHARS',
        'SPCHARS',
        'VCHARCHARS',
        'WSPCHARS',
        'ALPHA',
        'BIT',
        'CHAR',
        'CR',
        'CRLF',
        'CTL',
        'DIGIT',
        'DQUOTE',
        'HEXDIG',
        'HTAB',
        'LF',
        'LWSP',
        'OCTET',
        'SP',
        'VCHAR',
        'WSP',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getClassRuleNames() : array
    {
        return self::$rfc5234Rules;
    }
}

// vim: syntax=php sw=4 ts=4 et:
