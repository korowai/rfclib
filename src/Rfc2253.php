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
 * Syntax rules from [RFC2253](https://tools.ietf.org/html/rfc2253)
 * as PCRE regular expressions.
 *
 * **Example**:
 *
 * ```
 * $result = preg_match('/^'.Rfc2253::DISTINGUISHED_NAME.'$/', $subject, $matches, PREG_UNMATCHED_AS_NULL)
 * ```
 */
class Rfc2253 extends AbstractRuleSet
{
    // character lists for character classes
    public const ALPHACHARS = 'A-Za-z';
    public const DIGITCHARS = '0-9';
    public const HEXDIGCHARS = '0-9A-Fa-f';
    public const SPECIALCHARS = ',=+<>#;';
    public const KEYCHARCHARS = self::DIGITCHARS.self::ALPHACHARS.'-';

    //
    // character classes
    //

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * ALPHA =  <any ASCII alphabetic character> ; (decimal 65-90 and 97-122)
     * ```
     */
    public const ALPHA = '['.self::ALPHACHARS.']';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * DIGIT = <any ASCII decimal digit> ; (decimal 48-57)
     * ```
     */
    public const DIGIT = '['.self::DIGITCHARS.']';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * hexchar = DIGIT / "A" / "B" / "C" / "D" / "E" / "F" / "a" / "b" / "c" / "d" / "e" / "f"
     * ```
     */
    public const HEXCHAR = '['.self::HEXDIGCHARS.']';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * special = "," / "=" / "+" / "<" /  ">" / "#" / ";"
     * ```
     */
    public const SPECIAL = '['.self::SPECIALCHARS.']';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * keychar = ALPHA / DIGIT / "-"
     * ```
     */
    public const KEYCHAR = '['.self::KEYCHARCHARS.']';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * stringchar = <any character except one of special, "\" or QUOTATION >
     * ```
     */
    public const STRINGCHAR = '[^'.self::SPECIALCHARS.'\\\\"]';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * quotechar = <any character except "\" or QUOTATION >
     * ```
     */
    public const QUOTECHAR = '[^\\\\"]';

    //
    // productions
    //

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * hexpair = hexchar hexchar
     * ```
     */
    public const HEXPAIR = '(?:'.self::HEXCHAR.self::HEXCHAR.')';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * string_hex  = 1*hexpair
     * ```
     */
    public const HEXSTRING = '(?:'.self::HEXPAIR.'+)';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * pair = "\" ( special / "\" / QUOTATION / hexpair )
     * ```
     */
    public const PAIR = '(?:\\\\(?:['.self::SPECIALCHARS.'\\\\"]|'.self::HEXPAIR.'))';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * oid = 1*DIGIT *("." 1*DIGIT)
     * ```
     */
    public const OID = '(?:'.self::DIGIT.'+(?:\.'.self::DIGIT.'+)*)';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * string = *( stringchar / pair ) / "#" string_hex / QUOTATION *( quotechar / pair ) QUOTATION
     * ```
     */
    public const STRING =
        '(?:'.
            '(?:'.self::STRINGCHAR.'|'.self::PAIR.')*'.
            '|'.
            '(?:#'.self::HEXSTRING.')'.
            '|'.
            '(?:"(?:'.self::QUOTECHAR.'|'.self::PAIR.')*")'.
        ')';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * attributeValue = string
     * ```
     */
    public const ATTRIBUTE_VALUE = self::STRING;

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * attributeType = (ALPHA 1*keychar) / oid
     * ```
     */
    public const ATTRIBUTE_TYPE =
        '(?:'.
            // RFC2253 has bug here (1* instead of just *), so strict RFC2253
            // does not allow one-letter attribute types such as 'O'
            '(?:'.self::ALPHA.self::KEYCHAR.'*)|'.self::OID.
        ')';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * attributeTypeAndValue = attributeType "=" attributeValue
     * ```
     */
    public const ATTRIBUTE_TYPE_AND_VALUE = '(?:'.self::ATTRIBUTE_TYPE.'='.self::ATTRIBUTE_VALUE.')';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * name-component = attributeTypeAndValue *("+" attributeTypeAndValue)
     * ```
     */
    public const NAME_COMPONENT = '(?:'.self::ATTRIBUTE_TYPE_AND_VALUE.'(?:\+'.self::ATTRIBUTE_TYPE_AND_VALUE.')*)';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * name = name-component *("," name-component)
     * ```
     */
    public const NAME = '(?:'.self::NAME_COMPONENT.'(?:,'.self::NAME_COMPONENT.')*)';

    /**
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3):
     *
     * ```
     * distinguishedName = [name] ; may be empty string
     * ```
     *
     * Capture groups:
     *
     *  - ``dn``.
     */
    public const DISTINGUISHED_NAME = '(?<dn>'.self::NAME.'?)';

    /**
     * Names of RFC2253 rules.
     */
    protected static $rfc2253Rules = [
        'ALPHACHARS',
        'DIGITCHARS',
        'HEXDIGCHARS',
        'SPECIALCHARS',
        'KEYCHARCHARS',
        'ALPHA',
        'DIGIT',
        'HEXCHAR',
        'SPECIAL',
        'KEYCHAR',
        'STRINGCHAR',
        'QUOTECHAR',
        'HEXPAIR',
        'HEXSTRING',
        'PAIR',
        'OID',
        'STRING',
        'ATTRIBUTE_VALUE',
        'ATTRIBUTE_TYPE',
        'ATTRIBUTE_TYPE_AND_VALUE',
        'NAME_COMPONENT',
        'NAME',
        'DISTINGUISHED_NAME',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getClassRuleNames() : array
    {
        return self::$rfc2253Rules;
    }
}

// vim: syntax=php sw=4 ts=4 et:
