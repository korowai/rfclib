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
 * Syntax rules from [RFC2849](https://tools.ietf.org/html/rfc2849)
 * as PCRE regular expressions.
 *
 * **Example**:
 *
 * ```
 * $result = preg_match('/\G'.Rfc2849::ATTRVAL_SPEC.'/', $subject, $matches, PREG_UNMATCHED_AS_NULL)
 * ```
 */
class Rfc2849 extends AbstractRuleSet
{
    //
    // character sequences for character classes
    //

    /**
     * Same as [Rfc5234::ALPHACHARS](Rfc5234.html).
     */
    public const ALPHACHARS = Rfc5234::ALPHACHARS;

    /**
     * Same as [Rfc5234::DIGITCHARS](Rfc5234.html).
     */
    public const DIGITCHARS = Rfc5234::DIGITCHARS;

    //
    // character classes
    //

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``ALPHA = %x41-5A / %x61-7A``;
     * A-Z / a-z
     */
    public const ALPHA = Rfc5234::ALPHA;

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``DIGIT = %x30-39``;
     * 0-9
     */
    public const DIGIT = Rfc5234::DIGIT;

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``CR = %x0D``;
     * ASCII CR, carriage return
     */
    public const CR = Rfc5234::CR;

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``LF = %x0A``;
     * ASCII LF, line feed
     */
    public const LF = Rfc5234::LF;

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``SPACE = %x20``;
     * ASCII SP, space
     */
    public const SPACE = Rfc5234::SP;

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``attr-type-chars = ALPHA / DIGIT / "-"``
     */
    public const ATTR_TYPE_CHARS = '['.self::DIGITCHARS.self::ALPHACHARS.'-]';

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``BASE64-CHAR = %x2B / %x2F / %x30-39 / %x3D / %x41-5A / %x61-7A``;
     * +, /, 0-9, =, A-Z, and a-z
     */
    public const BASE64_CHAR = '[\+\/0-9=A-Za-z]';

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``opt-char = attr-type-chars``
     */
    public const OPT_CHAR = self::ATTR_TYPE_CHARS;

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``SAFE-CHAR = %x01-09 / %x0B-0C / %x0E-7F``;
     * any value <= 127 decimal except NUL, LF, and CR.
     */
    public const SAFE_CHAR = '[\x01-\x09\x0B-\x0C\x0E-\x7F]';

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``SAFE-INIT-CHAR = %x01-09 / %x0B-0C / %x0E-1F / %x21-39 / %x3B / %x3D-7F``;
     * any value <= 127 decimal except NUL, LF, CR, SPACE, colon (":") and less
     * than ("<").
     */
    public const SAFE_INIT_CHAR = '[\x01-\x09\x0B-\x0C\x0E-\x1F\x21-\x39\x3B\x3D-\x7F]';

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``SEP = (CR LF / LF)``
     */
    public const SEP = '(?:'.self::CR.self::LF.'|'.self::LF.')';

    /**
     * Matches [SEP](Rfc2849.html) or end of string ``$``.
     */
    public const EOL = '(?:'.self::SEP.'|$)';

    /**
     * Matches any character except [EOL](Rfc2849.html).
     */
    public const NOTEOL = '(?:[^'.self::CR.self::LF.']|'.self::CR.'(?!'.self::LF.'))';

    //
    // productions
    //

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``FILL = *SPACE``
     */
    public const FILL = '(?:'.self::SPACE.'*)';

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``version-number = 1*DIGIT``
     */
    public const VERSION_NUMBER = '(?:'.self::DIGIT.'+)';
    // [/VERSION_NUMBER]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``version-spec = "version:" FILL version-number``
     *
     * Capture groups:
     *
     * - ``version_number``
     * - ``version_error``
     */
    public const VERSION_SPEC =
        '(?:'.
            'version:'.self::FILL.
            '(?:'.
                '(?<version_number>'.self::VERSION_NUMBER.')'.
                '|'.
                '(?:'.self::VERSION_NUMBER.'?(?<version_error>'.self::NOTEOL.'*))'.
            ')'.
            '(?='.self::EOL.')'.
        ')';
    // [/VERSION_SPEC]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``BASE64-STRING = [*(BASE64-CHAR)]``
     */
    public const BASE64_STRING = '(?:'.self::BASE64_CHAR.'*)';
    // [/BASE64_STRING]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``BASE64-UTF8-STRING = BASE64-STRING``
     */
    public const BASE64_UTF8_STRING = self::BASE64_STRING;
    // [/BASE64_UTF8_STRING]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849)
     * ``SAFE-STRING = [SAFE-INIT-CHAR *SAFE-CHAR]``
     */
    public const SAFE_STRING = '(?:(?:'.self::SAFE_INIT_CHAR.self::SAFE_CHAR.'*)?)';
    // [/SAFE_STRING]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``ldap-oid = 1*DIGIT *("." 1*DIGIT)``;
     * An LDAPOID, as defined in [RFC2251](https://tools.ietf.org/html/rfc2251)
     */
    public const LDAP_OID = Rfc2253::OID;
    // [/LDAP_OID]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``option = 1*opt-char``;
     */
    public const OPTION = '(?:'.self::OPT_CHAR.'+)';
    // [/OPTION]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``options = option / (option ";" options)``
     */
    public const OPTIONS = '(?:'.self::OPTION.'(?:;'.self::OPTION.')*)';
    // [/OPTIONS]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``AttributeType = ldap-oid / (ALPHA *(attr-type-chars))``
     */
    public const ATTRIBUTE_TYPE = '(?:'.self::LDAP_OID.'|(?:'.self::ALPHA.self::ATTR_TYPE_CHARS.'*))';
    // [/ATTRIBUTE_TYPE]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``AttributeDescription = AttributeType [";" options]``
     */
    public const ATTRIBUTE_DESCRIPTION = '(?:'.self::ATTRIBUTE_TYPE.'(?:;'.self::OPTIONS.')?)';
    // [/ATTRIBUTE_DESCRIPTION]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``distinguishedName = SAFE-STRING``
     */
    public const DISTINGUISHED_NAME = self::SAFE_STRING;
    // [/DISTINGUISHED_NAME]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``base64-distinguishedName = SAFE-STRING``
     */
    public const BASE64_DISTINGUISHED_NAME = self::BASE64_UTF8_STRING;
    // [/BASE64_DISTINGUISHED_NAME]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``rdn = SAFE-STRING``;
     * a relative distinguished name defined as name-component in
     * [RFC2253](https://tools.ietf.org/html/rfc2253#section-3)
     */
    public const RDN = self::SAFE_STRING;
    // [/RDN]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``base64-rdn = BASE64-UTF8-STRING``;
     * an rdn which has been base64 encoded
     */
    public const BASE64_RDN = self::BASE64_UTF8_STRING;
    // [/BASE64_RDN]

    /**
     * ``value-safe = SAFE-STRING``
     *
     * Capture groups:
     *
     *  - ``value_safe``
     *  - ``value_safe_error``
     */
    public const VALUE_SAFE =
        '(?:(?![:<])'.
            self::FILL.
            '(?:'.
                '(?<value_safe>'.self::SAFE_STRING.')'.
                '|'.
                '(?:'.self::SAFE_STRING.'(?<value_safe_error>'.self::NOTEOL.'*))'.
            ')'.
            '(?='.self::EOL.')'.
        ')';
    // [/VALUE_SAFE]

    /**
     * ``value-base64 = BASE64-STRING``
     *
     * Capture groups:
     *
     *  - ``value_b64``
     *  - ``value_b64_error``
     */
    public const VALUE_BASE64 =
        '(?::'.
            self::FILL.
            '(?:'.
                '(?<value_b64>'.self::BASE64_STRING.')'.
                '|'.
                '(?:'.self::BASE64_STRING.'(?<value_b64_error>'.self::NOTEOL.'*))'.
            ')'.
            '(?='.self::EOL.')'.
        ')';
    // [/VALUE_BASE64]

    /**
     * ``value-url = URL``
     *
     * Capture groups
     *
     *  - ``value_url``
     *  - ``value_url_error``
     */
    public const VALUE_URL =
        '(?:<'.
            self::FILL.
            '(?:(?J)'.
                '(?<value_url>'.self::URL.')'.
                '|'.
                '(?:(?:'.self::URL.')?(?<value_url_error>'.self::NOTEOL.'*))'.
            ')'.
            '(?='.self::EOL.')'.
        ')';
    // [/VALUE_URL]


    /**
     * ``dn-value-spec = ":" ( FILL distinguishedName / ":" FILL base64-distinguishedName )``
     *
     * Capture groups:
     *
     *  - ``value_safe``
     *  - ``value_b64``
     *  - ``value_safe_error``
     *  - ``value_b64_error``
     */
    public const DN_VALUE_SPEC =
        '(?:'.
            ':'.
            '(?:'.
                self::VALUE_SAFE.
                '|'.
                self::VALUE_BASE64.
            ')'.
        ')';
    // [/DN_VALUE_SPEC]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``dn-spec = "dn:" (FILL distinguishedName / ":" FILL base64-distinguishedName)``
     *
     * Capture groups:
     *
     *  - ``value_safe``
     *  - ``value_b64``
     */
    public const DN_SPEC = '(?:dn'.self::DN_VALUE_SPEC.')';
    // [/DN_SPEC]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``url = <a URL as defined in `` [RFC1738](https://tools.ietf.org/html/rfc1738) `` >``;
     * (we use URI-reference from [RFC3986](https://tools.ietf.org/html/rfc3986) instead of RFC1738)
     */
    public const URL = Rfc3986::URI_REFERENCE;
    // [/URL]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``value-spec = ":" (FILL 0*1(SAFE-STRING) / ":" FILL (BASE64-STRING) / "<" FILL url)``
     *
     * Matches any string that starts with one of ``":"``, ``"::"`` or ``":<"``
     * and spans to the nearest [EOL](Rfc2849.html). Returns one of
     * the ``value_*_error`` capture groups (below) if there is an error after
     * the ``":"``/``"::"``/``":<"`` tag.
     *
     * Capture groups:
     *
     *  - ``value_safe``
     *  - ``value_b64``
     *  - ``value_url``
     *  - ``value_safe_error``
     *  - ``value_b64_error``
     *  - ``value_url_error``
     */
    public const VALUE_SPEC =
        '(?:'.
            ':'.
            '(?:'.
                self::VALUE_SAFE.
                '|'.
                self::VALUE_BASE64.
                '|'.
                self::VALUE_URL.
            ')'.
        ')';
    // [/VALUE_SPEC]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``control = "control:" FILL ldap-oid 0*1(1*SPACE ("true" / "false")) 0*1(value-spec) SEP``
     *
     * Matches any string that starts with ``"control:"`` tag and spans to the
     * nearest [EOL](Rfc2849.html).
     *
     * Capture groups:
     *
     *  - ``ctl_type``
     *  - ``ctl_crit``
     *  - ``value_safe``
     *  - ``value_b64``
     *  - ``value_url``
     *  - ``ctl_type_error``
     *  - ``ctl_crit_error``
     *  - ``value_safe_error``
     *  - ``value_b64_error``
     *  - ``value_url_error``
     */
    public const CONTROL =
        '(?:'.
            'control:'.self::FILL.'(?:(?:'.
                '(?<ctl_type>'.self::LDAP_OID.')'.
                '(?:'.self::SPACE.'+(?<ctl_crit>true|false))?'.
                '(?:'.self::VALUE_SPEC.')?'.
            ')|(?:'.
                '(?:'.self::LDAP_OID.')'.
                '(?:'.self::SPACE.'+)'.
                '(?<ctl_crit_error>'.self::NOTEOL.'*)'.
            ')|(?:'.
                '(?:'.self::LDAP_OID.')?'.
                '(?<ctl_type_error>'.self::NOTEOL.'*)'.
            '))'.
            self::EOL.
        ')';
    // [/CONTROL]


    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``attrval-spec = AttributeDescription value-spec SEP``
     *
     * Capture groups:
     *
     *  - ``attr_desc``
     *  - ``value_safe``
     *  - ``value_b64``
     *  - ``value_url``
     *  - ``value_safe_error``
     *  - ``value_b64_error``
     *  - ``value_url_error``
     */
    public const ATTRVAL_SPEC =
        '(?:'.
            '(?<attr_desc>'.self::ATTRIBUTE_DESCRIPTION.')'.
            self::VALUE_SPEC.
            self::EOL.
        ')';
    // [/ATTRVAL_SPEC]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``mod-spec = ("add:" / "delete:" / "replace:") FILL AttributeDescription SEP *attrval-spec "-" SEP``
     *
     * This pattern implements the initial line of the *mod-spec* rule (call it
     * *mod-spec-init*), such that::
     *
     *      mod-spec = mod-spec-init SEP *attrval-spec "-" SEP
     *      mod-spec-init = ("add:" / "delete:" / "replace:") FILL AttributeDescription
     *
     * Capture groups:
     *
     *  - ``mod_type``
     *  - ``attr_desc``
     *  - ``attr_type_error``
     *  - ``attr_opts_error``
     */
    public const MOD_SPEC_INIT =
        '(?:'.
            '(?<mod_type>add|delete|replace):'.
            self::FILL.
            '(?:'.
                '(?:'.
                    '(?<attr_desc>'.self::ATTRIBUTE_DESCRIPTION.')'.
                ')'.
                '|'.
                '(?:'.
                    self::ATTRIBUTE_TYPE.
                    '(?:;'.self::OPTIONS.'?)'.
                    '(?<attr_opts_error>'.self::NOTEOL.'*)'.
                ')'.
                '|'.
                '(?:'.
                    self::ATTRIBUTE_TYPE.'?'.
                    '(?<attr_type_error>'.self::NOTEOL.'*'.')'.
                ')'.
            ')'.
            '(?:'.self::EOL.')'.
        ')';
    // [/MOD_SPEC_INIT]

    /**
     * [RFC2849](https://tools.ietf.org/html/rfc2849):
     * ``changerecord = "changetype:" FILL (change-add / change-delete / change-modify / change-moddn)``
     *
     * This pattern implements the initial line of the *changerecord* rule (call it *changerecord-init*),
     * such that:
     *
     * ```
     * changerecord-init = "changetype:" FILL ("add" / "delete" / "modrdn" / "moddn" / "modify") SEP
     * ```
     *
     * Capture groups:
     *
     *  - ``chg_type``
     *  - ``chg_type_error``
     */
    public const CHANGERECORD_INIT =
        '(?:'.
            'changetype:'.self::FILL.
            '(?:'.
                '(?<chg_type>add|delete|modify|modrdn|moddn)'.
                '|'.
                '(?<chg_type_error>'.self::NOTEOL.'*)'.
            ')'.
            self::EOL.
        ')';
    // [/CHANGERECORD_INIT]

    /**
     * Matches the following production and provides enhanced error detection.
     *
     * ``newrdn-spec = "newrdn:" ( FILL rdn / ":" FILL base64-rdn ) SEP``
     *
     * Capture groups:
     *
     *  - ``value_safe``
     *  - ``value_b64``
     *  - ``value_safe_error``
     *  - ``value_b64_error``
     */
    public const NEWRDN_SPEC = '(?:newrdn'.self::DN_VALUE_SPEC.self::EOL.')';
    // [/NEWRDN_SPEC]

    /**
     * ``newsuperior-spec = "newsuperior:" (FILL distinguishedName / ":" FILL base64-distinguishedName) SEP``
     *
     * Matches any string that starts with ``"newsuperior:"``, and spans to the nearest
     * [EOL](Rfc2849.html) including it. Returns one of the ``value_*_error``
     * capture groups if there is an error after the ``"version:"`` tag.
     *
     * Capture groups:
     *
     *  - ``value_safe``
     *  - ``value_b64``
     *  - ``value_safe_error``
     *  - ``value_b64_error``
     */
    public const NEWSUPERIOR_SPEC = '(?:newsuperior'.self::DN_VALUE_SPEC.self::EOL.')';
    // [/NEWSUPERIOR_SPEC]

    /**
     * Rules provided by this class.
     */
    protected static $rfc2849Rules = [
        'ALPHACHARS',
        'DIGITCHARS',
        'ALPHA',
        'DIGIT',
        'CR',
        'LF',
        'SPACE',
        'ATTR_TYPE_CHARS',
        'BASE64_CHAR',
        'OPT_CHAR',
        'SAFE_CHAR',
        'SAFE_INIT_CHAR',
        'SEP',
        'EOL',
        'NOTEOL',
        'FILL',
        'VERSION_NUMBER',
        'VERSION_SPEC',
        'BASE64_STRING',
        'BASE64_UTF8_STRING',
        'SAFE_STRING',
        'LDAP_OID',
        'OPTION',
        'OPTIONS',
        'ATTRIBUTE_TYPE',
        'ATTRIBUTE_DESCRIPTION',
        'DISTINGUISHED_NAME',
        'BASE64_DISTINGUISHED_NAME',
        'RDN',
        'BASE64_RDN',
        'VALUE_SAFE',
        'VALUE_BASE64',
        'VALUE_URL',
        'DN_VALUE_SPEC',
        'DN_SPEC',
        'URL',
        'VALUE_SPEC',
        'CONTROL',
        'ATTRVAL_SPEC',
        'MOD_SPEC_INIT',
        'CHANGERECORD_INIT',
        'NEWRDN_SPEC',
        'NEWSUPERIOR_SPEC',
    ];

    /**
     * Error messages. Error messages defined only for productions that are
     * expected to be used as standalone rules in parser.
     */
    protected static $rfc2849Errors = [
        '' => [
//            'ALPHACHARS'                => 'expected ALPHACHARS (RFC2849)',
//            'DIGITCHARS'                => 'expected DIGITCHARS (RFC2849)',
//            'ALPHA'                     => 'expected ALPHA (RFC2849)',
//            'DIGIT'                     => 'expected DIGIT (RFC2849)',
//            'CR'                        => 'expected CR (RFC2849)',
//            'LF'                        => 'expected LF (RFC2849)',
//            'SPACE'                     => 'expected SPACE (RFC2849)',
//            'ATTR_TYPE_CHARS'           => 'expected attr-type-chars (RFC2849)',
//            'BASE64_CHAR'               => 'expected BASE64-CHAR (RFC2849)',
//            'OPT_CHAR'                  => 'expected OPT-CHAR (RFC2849)',
//            'SAFE_CHAR'                 => 'expected SAFE-CHAR (RFC2849)',
//            'SAFE_INIT_CHAR'            => 'expected SAFE-INIT-CHAR (RFC2849)',
            'SEP'                       => 'expected line separator (RFC2849)',
////            'FILL'                      => 'expected FILL (RFC2849)',
////            'VERSION_NUMBER'            => 'expected version-number (RFC2849)',
////            'VERSION_SPEC'              => 'expected version-spec (RFC2849)',
////            'BASE64_STRING'             => 'expected BASE64-STRING (RFC2849)',
////            'BASE64_UTF8_STRING'        => 'expected BASE64-UTF8-STRING (RFC2849)',
////            'SAFE_STRING'               => 'expected SAFE-STRING (RFC2849)',
////            'LDAP_OID'                  => 'expected ldap-oid (RFC2849)',
////            'OPTION'                    => 'expected option (RFC2849)',
////            'OPTIONS'                   => 'expected options (RFC2849)',
////            'ATTRIBUTE_TYPE'            => 'expected AttributeType (RFC2849)',
////            'ATTRIBUTE_DESCRIPTION'     => 'expected AttributeDescription (RFC2849)',
////            'DISTINGUISHED_NAME'        => 'expected distinguishedName (RFC2849)',
////            'BASE64_DISTINGUISHED_NAME' => 'expected base64-distinguishedName (RFC2849)',
////            'RDN'                       => 'expected rdn (RFC2849)',
////            'BASE64_RDN'                => 'expected base64-rdn (RFC2849)',
////            'DN_SPEC'                   => 'expected dn-spec (RFC2849)',
////            'URL'                       => 'expected URL (RFC2849)',
////            'VALUE_SPEC'                => 'expected value-spec (RFC2849)',
////            'CONTROL'                   => 'expected control (RFC2849)',
////            'ATTRVAL_SPEC'              => 'expected attrval-spec (RFC2849)',
            'VERSION_SPEC'          => 'expected "version:" (RFC2849)',
            'DN_SPEC'               => 'expected "dn:" (RFC2849)',
            'VALUE_SPEC'            => 'expected ":" (RFC2849)',
            'CONTROL'               => 'expected "control:" (RFC2849)',
            'ATTRVAL_SPEC'          => 'expected <AttributeDescription>":" (RFC2849)',
            'MOD_SPEC_INIT'         => 'expected one of "add:", "delete:" or "replace:" (RFC2849)',
            'CHANGERECORD_INIT'     => 'expected "changetype:" (RFC2849)',
            'NEWRDN_SPEC'           => 'expected "newrdn:" (RFC2849)',
            'NEWSUPERIOR_SPEC'      => 'expected "newsuperior:" (RFC2849)',
        ],
        'attr_opts_error'   => 'missing or invalid options (RFC2849)',
        'attr_type_error'   => 'missing or invalid AttributeType (RFC2849)',
        'chg_type_error'    => 'missing or invalid change type (RFC2849)',
        'ctl_type_error'    => 'missing or invalid OID (RFC2849)',
        'ctl_crit_error'    => 'expected "true" or "false" (RFC2849)',
        'value_b64_error'   => 'malformed BASE64-STRING (RFC2849)',
        'value_safe_error'  => 'malformed SAFE-STRING (RFC2849)',
        'value_url_error'   => 'malformed URL (RFC2849/RFC3986)',
        'version_error'     => 'expected valid version number (RFC2849)',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getClassRuleNames() : array
    {
        return self::$rfc2849Rules;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefinedErrors() : array
    {
        return array_merge_recursive(parent::getDefinedErrors(), self::$rfc2849Errors);
    }
}

// vim: syntax=php sw=4 ts=4 et:
