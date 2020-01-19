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
 * Syntax rules from [RFC3986](https://tools.ietf.org/html/rfc3986)
 * as PCRE regular expressions.
 *
 * **Example**:
 *
 * ```
 * $result = preg_match('/^'.Rfc3986::URI_REFERENCE.'$/', $subject, $matches, PREG_UNMATCHED_AS_NULL)
 * ```
 *
 * @link https://tools.ietf.org/html/rfc3986 (the URI specification)
 */
class Rfc3986 extends AbstractRuleSet
{
    // character lists for character classes
    /**
     * Same as [Rfc5234::ALPHACHARS](Rfc5234.html)
     */
    public const ALPHACHARS = Rfc5234::ALPHACHARS;
    /**
     * Same as [Rfc5234::DIGITCHARS](Rfc5234.html)
     */
    public const DIGITCHARS = Rfc5234::DIGITCHARS;
    /**
     * Same as [Rfc5234::HEXDIGCHARS](Rfc5234.html)
     */
    public const HEXDIGCHARS = Rfc5234::HEXDIGCHARS.'a-f';
    public const GEN_DELIM_CHARS = ':\/\?#\[\]@';
    public const SUB_DELIM_CHARS = '!\$&\'\(\)\*\+,;=';
    public const RESERVEDCHARS = self::GEN_DELIM_CHARS.self::SUB_DELIM_CHARS;
    public const UNRESERVEDCHARS = self::ALPHACHARS.self::DIGITCHARS.'\._~-';
    public const PCHARCHARS = ':@'.self::SUB_DELIM_CHARS.self::UNRESERVEDCHARS;

    // character classes
    /**
     * Same as [Rfc5234::ALPHA](Rfc5234.html)
     */
    public const ALPHA = Rfc5234::ALPHA;

    /**
     * Same as [Rfc5234::DIGIT](Rfc5234.html)
     */
    public const DIGIT = Rfc5234::DIGIT;

    /**
     * Same as [Rfc5234::HEXDIG](Rfc5234.html)
     */
    public const HEXDIG = '['.self::HEXDIGCHARS.']';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-2.2):
     *
     * ```
     * sub-delims  = "!" / "$" / "&" / "'" / "(" / ")"
     *             / "*" / "+" / "," / ";" / "="
     * ```
     */
    public const SUB_DELIMS = '['.self::SUB_DELIM_CHARS.']';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-2.2):
     *
     * ```
     * gen-delims = ":" / "/" / "?" / "#" / "[" / "]" / "@"
     * ```
     */
    public const GEN_DELIMS = '['.self::GEN_DELIM_CHARS.']';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-2.2):
     *
     * ```
     * reserved = gen-delims / sub-delims
     * ```
     */
    public const RESERVED = '['.self::RESERVEDCHARS.']';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-2.3):
     *
     * ```
     * unreserved = ALPHA / DIGIT / "-" / "." / "_" / "~"
     * ```
     */
    public const UNRESERVED = '['.self::UNRESERVEDCHARS.']';

    // (sub)expressions
    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-2.1):
     *
     * ```
     * pct-encoded = "%" HEXDIG HEXDIG
     *
     * ```
     */
    public const PCT_ENCODED = '(?:%'.self::HEXDIG.self::HEXDIG.')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * pchar = unreserved / pct-encoded / sub-delims / ":" / "@"
     * ```
     */
    public const PCHAR = '(?:['.self::PCHARCHARS.']|'.self::PCT_ENCODED.')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * segment-nz-nc = 1*( unreserved / pct-encoded / sub-delims / "@" )
     *               ; non-zero-length segment without any colon ":"
     * ```
     */
    public const SEGMENT_NZ_NC = '(?:(?:[@'.self::SUB_DELIM_CHARS.self::UNRESERVEDCHARS.']|'.self::PCT_ENCODED.')+)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * segment-nz = 1*pchar
     * ```
     */
    public const SEGMENT_NZ = '(?:'.self::PCHAR.'+)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * segment = *pchar
     * ```
     */
    public const SEGMENT = '(?:'.self::PCHAR.'*)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * path-empty    = 0<pchar>
     * ```
     *
     * Captures:
     *
     * - ``path_empty``
     */
    public const PATH_EMPTY = '(?<path_empty>)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * path-noscheme = segment-nz-nc *( "/" segment )
     * ```
     *
     * Captures:
     *
     * - ``path_noscheme``
     */
    public const PATH_NOSCHEME = '(?<path_noscheme>'.self::SEGMENT_NZ_NC.'(?:\/'.self::SEGMENT.')*)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * path-rootless = segment-nz *( "/" segment )
     * ```
     *
     * Captures:
     *
     * - ``path_rootless``
     */
    public const PATH_ROOTLESS = '(?<path_rootless>'.self::SEGMENT_NZ.'(?:\/'.self::SEGMENT.')*)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * path-absolute = "/" [ segment-nz *( "/" segment ) ]
     * ```
     *
     * Captures:
     *
     * - ``path_absolute``
     */
    public const PATH_ABSOLUTE = '(?<path_absolute>\/(?:'.self::SEGMENT_NZ.'(?:\/'.self::SEGMENT.')*)?)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.3):
     *
     * ```
     * path-abempty  = *( "/" segment )
     * ```
     *
     * Captures:
     *
     * - ``path_abempty``
     */
    public const PATH_ABEMPTY = '(?<path_abempty>(?:\/'.self::SEGMENT.')*)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * reg-name = *( unreserved / pct-encoded / sub-delims )
     * ```
     *
     * Captures:
     *
     * - ``reg_name``
     */
    public const REG_NAME =
        '(?<reg_name>'.
            '(?:['.self::SUB_DELIM_CHARS.self::UNRESERVEDCHARS.']|'.self::PCT_ENCODED.')*'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * dec-octet = DIGIT                 ; 0-9
     *           / %x31-39 DIGIT         ; 10-99
     *           / "1" 2DIGIT            ; 100-199
     *           / "2" %x30-34 DIGIT     ; 200-249
     *           / "25" %x30-35          ; 250-255
     * ```
     */
    public const DEC_OCTET =
        '(?:'.
            self::DIGIT.                    // 0-9
            '|'.
            '[1-9]'.self::DIGIT.            // 10-99
            '|'.
            '1'.self::DIGIT.self::DIGIT.    // 100-199
            '|'.
            '2[0-4]'.self::DIGIT.           // 200-249
            '|'.
            '25[0-5]'.                      // 250-255
        ')';

    /**
     * Re-used in IPV4ADDRESS and IPV6V4ADDRESS.
     */
    public const DEC4OCTETS =
        '(?:'.
                 self::DEC_OCTET.
            '\.'.self::DEC_OCTET.
            '\.'.self::DEC_OCTET.
            '\.'.self::DEC_OCTET.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * IPv4address = dec-octet "." dec-octet "." dec-octet "." dec-octet
     * ```
     *
     * Captures:
     *
     * - ``ipv4address``.
     */
    public const IPV4ADDRESS = '(?<ipv4address>'.self::DEC4OCTETS.')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * IPv4address = dec-octet "." dec-octet "." dec-octet "." dec-octet
     * ```
     *
     * Captures:
     *
     * - ``ipv6v4address``
     */
    public const IPV6V4ADDRESS = '(?<ipv6v4address>'.self::DEC4OCTETS.')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * h16 = 1*4HEXDIG
     *     ; 16 bits of address represented in hexadecimal
     * ```
     */
    public const H16 = '(?:'.self::HEXDIG.'{1,4})';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * ls32 = ( h16 ":" h16 ) / IPv4address
     *      ; least-significant 32 bits of address
     * ```
     *
     * Captures:
     *
     * - ``ls32``,
     *      - ``ipv6v4address``.
     */
    public const LS32 = '(?<ls32>(?:'.self::H16.':'.self::H16.')|'.self::IPV6V4ADDRESS.')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * IPv6address =                            6( h16 ":" ) ls32
     *             /                       "::" 5( h16 ":" ) ls32
     *             / [               h16 ] "::" 4( h16 ":" ) ls32
     *             / [ *1( h16 ":" ) h16 ] "::" 3( h16 ":" ) ls32
     *             / [ *2( h16 ":" ) h16 ] "::" 2( h16 ":" ) ls32
     *             / [ *3( h16 ":" ) h16 ] "::"    h16 ":"   ls32
     *             / [ *4( h16 ":" ) h16 ] "::"              ls32
     *             / [ *5( h16 ":" ) h16 ] "::"              h16
     *             / [ *6( h16 ":" ) h16 ] "::"
     * ```
     *
     * Captures:
     *
     * - ``ipv6address``,
     *      - ``ls32``,
     *          - ``ipv6v4address``.
     */
    public const IPV6ADDRESS =
        '(?<ipv6address>(?|'.
             '(?:'.                                                    '(?:'.self::H16.':){6,6}'.self::LS32.')'.
            '|(?:'.                                                  '::(?:'.self::H16.':){5,5}'.self::LS32.')'.
            '|(?:'.                           '(?:'.self::H16.')?'.  '::(?:'.self::H16.':){4,4}'.self::LS32.')'.
            '|(?:'.    '(?:(?:'.self::H16.':){0,1}'.self::H16.')?'.  '::(?:'.self::H16.':){3,3}'.self::LS32.')'.
            '|(?:'.    '(?:(?:'.self::H16.':){0,2}'.self::H16.')?'.  '::(?:'.self::H16.':){2,2}'.self::LS32.')'.
            '|(?:'.    '(?:(?:'.self::H16.':){0,3}'.self::H16.')?'.  '::(?:'.self::H16.':){1,1}'.self::LS32.')'.
            '|(?:'.    '(?:(?:'.self::H16.':){0,4}'.self::H16.')?'.  '::'.self::LS32.')'.
            '|(?:'.    '(?:(?:'.self::H16.':){0,5}'.self::H16.')?'.  '::'.self::H16.')'.
            '|(?:'.    '(?:(?:'.self::H16.':){0,6}'.self::H16.')?'.  '::)'.
        '))';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * IPvFuture = "v" 1*HEXDIG "." 1*( unreserved / sub-delims / ":" )
     * ```
     *
     * Captures:
     *
     * - ``ipvfuture``.
     */
    public const IPVFUTURE =
        '(?<ipvfuture>'.
            'v'.self::HEXDIG.'+'.
            '\.[:'.self::SUB_DELIM_CHARS.self::UNRESERVEDCHARS.']+'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * IP-literal = "[" ( IPv6address / IPvFuture  ) "]"
     * ```
     *
     * Captures:
     *
     * - ``ip_literal``,
     *      - ``ipv6address``,
     *          - ``ls32``,
     *              - ``ipv6v4address``,
     *      - ``ipvfuture``.
     */
    public const IP_LITERAL =
        '(?<ip_literal>'.
            '\['.self::IPV6ADDRESS.'\]'.
            '|'.
            '\['.self::IPVFUTURE.'\]'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.3):
     *
     * ```
     * port = *DIGIT
     * ```
     *
     * Captures:
     *
     * - ``port``.
     */
    public const PORT =
        '(?<port>'.
            self::DIGIT.'*'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.2):
     *
     * ```
     * host = IP-literal / IPv4address / reg-name
     * ```
     *
     * Captures:
     *
     * - ``host``.
     *      - ``ip_literal``,
     *          - ``ipv6address``,
     *              - ``ls32``,
     *                  - ``ipv6v4address``,
     *          - ``ipvfuture``,
     *      - ``ipv4address``,
     *      - ``reg_name``.
     */
    public const HOST =
        '(?<host>'.
            self::IP_LITERAL.
            '|'.
            self::IPV4ADDRESS.
            '|'.
            self::REG_NAME.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2.1):
     *
     * ```
     * userinfo = *(unreserved / pct-encoded / sub-delims / ":")
     * ```
     *
     * Captures:
     *
     * - ``userinfo``.
     */
    public const USERINFO =
        '(?<userinfo>'.
            '(?:[:'.self::SUB_DELIM_CHARS.self::UNRESERVEDCHARS.']|'.self::PCT_ENCODED.')*'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.2):
     *
     * ```
     * authority = [ userinfo "@" ] host [ ":" port ]
     * ```
     *
     * Captures:
     *
     * - ``authority``,
     *      - ``userinfo``,
     *      - ``host``,
     *          - ``ip_literal``,
     *              - ``ipv6address``,
     *                  - ``ls32``,
     *                      - ``ipv6v4address``,
     *              - ``ipvfuture``,
     *          - ``ipv4address``,
     *          - ``reg_name``,
     *      - ``port``.
     */
    public const AUTHORITY =
        '(?<authority>'.
            '(?:'.self::USERINFO.'@)?'.
            self::HOST.
            '(?::'.self::PORT.')?'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.1):
     *
     * ```
     * scheme = ALPHA *( ALPHA / DIGIT / "+" / "-" / ".")
     * ```
     *
     * Captures:
     *
     * - ``scheme``.
     */
    public const SCHEME =
        '(?<scheme>'.
            self::ALPHA.'['.self::ALPHACHARS.self::DIGITCHARS.'\+\.-]*'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-4.2):
     *
     * ```
     * relative-part = "//" authority path-abempty
     *               / path-absolute
     *               / path-noscheme
     *               / path-empty
     * ```
     *
     * Captures:
     *
     * - ``relative_part``,
     *      - ``authority``,
     *          - ``userinfo``,
     *          - ``host``,
     *              - ``ip_literal``,
     *                  - ``ipv6address``,
     *                      - ``ls32``,
     *                          - ``ipv6v4address``,
     *                  - ``ipvfuture``,
     *              - ``ipv4address``,
     *              - ``reg_name``,
     *      - ``path_abempty``,
     *      - ``path_absolute``,
     *      - ``path_noscheme``,
     *      - ``path_empty``.
     */
    public const RELATIVE_PART =
        '(?<relative_part>'.
            '(?:\/\/'.self::AUTHORITY.self::PATH_ABEMPTY.')'.
            '|'.
            self::PATH_ABSOLUTE.
            '|'.
            self::PATH_NOSCHEME.
            //'|'.
            //self::PATH_ABEMPTY. // +errata [5428] (rejected)
            '|'.
            self::PATH_EMPTY.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3):
     *
     * ```
     * hier-part = "//" authority path-abempty
     *           / path-absolute
     *           / path-rootless
     *           / path-empty
     * ```
     *
     * Captures:
     *
     * - ``hier_part``,
     *      - ``authority``,
     *          - ``userinfo``,
     *          - ``host``,
     *              - ``ip_literal``,
     *                  - ``ipv6address``,
     *                      - ``ls32``,
     *                          - ``ipv6v4address``,
     *                  - ``ipvfuture``,
     *              - ``ipv4address``,
     *              - ``reg_name``,
     *      - ``path_abempty``,
     *      - ``path_absolute``,
     *      - ``path_rootless``,
     *      - ``path_empty``.
     */
    public const HIER_PART =
        '(?<hier_part>'.
            '(?:\/\/'.self::AUTHORITY.self::PATH_ABEMPTY.')'.
            '|'.
            self::PATH_ABSOLUTE.
            '|'.
            self::PATH_ROOTLESS.
            '|'.
            self::PATH_EMPTY.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.5):
     *
     * ```
     * fragment = *( pchar / "/" / "?" )
     * ```
     *
     * Captures:
     *
     * - ``fragment``.
     */
    public const FRAGMENT = '(?<fragment>(?:'.self::PCHAR.'|\/|\?)*)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3.4):
     *
     * ```
     * query = *( pchar / "/" / "?" )
     * ```
     *
     * Captures:
     *
     * - ``query``.
     */
    public const QUERY = '(?<query>(?:'.self::PCHAR.'|\/|\?)*)';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-4.2):
     *
     * ```
     * relative-ref = relative-part [ "?" query ] [ "#" fragment ]
     * ```
     *
     * Captures:
     *
     * - ``relative_ref``,
     *      - ``relative_part``,
     *           - ``authority``,
     *               - ``userinfo``,
     *               - ``host``,
     *                   - ``ip_literal``,
     *                       - ``ipv6address``,
     *                           - ``ls32``,
     *                               - ``ipv6v4address``,
     *                       - ``ipvfuture``,
     *                   - ``ipv4address``,
     *                   - ``reg_name``,
     *           - ``path_abempty``,
     *           - ``path_absolute``,
     *           - ``path_noscheme``,
     *           - ``path_empty``.
     *      - ``query``,
     *      - ``fragment``.
     */
    public const RELATIVE_REF =
        '(?<relative_ref>'.
            self::RELATIVE_PART.
            '(?:\?'.self::QUERY.')?'.
            '(?:#'.self::FRAGMENT.')?'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-4.3):
     *
     * ```
     * absolute-URI = scheme ":" hier-part [ "?" query ]
     * ```
     *
     * Captures:
     *
     * - ``absolute_uri``,
     *      - ``scheme``,
     *      - ``hier_part``,
     *           - ``authority``,
     *               - ``userinfo``,
     *               - ``host``,
     *                   - ``ip_literal``,
     *                       - ``ipv6address``,
     *                           - ``ls32``,
     *                               - ``ipv6v4address``,
     *                       - ``ipvfuture``,
     *                   - ``ipv4address``,
     *                   - ``reg_name``,
     *           - ``path_abempty``,
     *           - ``path_absolute``,
     *           - ``path_rootless``,
     *           - ``path_empty``.
     *      - ``query``.
     */
    public const ABSOLUTE_URI =
        '(?<absolute_uri>'.
            self::SCHEME.
            ':'.
            self::HIER_PART.
            '(?:\?'.self::QUERY.')?'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-3):
     *
     * ```
     * URI = scheme ":" hier-part [ "?" query ] [ "#" fragment ]
     * ```
     *
     * Captures:
     *
     * - ``uri``,
     *      - ``scheme``,
     *      - ``hier_part``,
     *           - ``authority``,
     *               - ``userinfo``,
     *               - ``host``,
     *                   - ``ip_literal``,
     *                       - ``ipv6address``,
     *                           - ``ls32``,
     *                               - ``ipv6v4address``,
     *                       - ``ipvfuture``,
     *                   - ``ipv4address``,
     *                   - ``reg_name``,
     *           - ``path_abempty``,
     *           - ``path_absolute``,
     *           - ``path_rootless``,
     *           - ``path_empty``.
     *      - ``query``,
     *      - ``fragment``.
     */
    public const URI =
        '(?<uri>'.
            self::SCHEME.
            ':'.
            self::HIER_PART.
            '(?:\?'.self::QUERY.')?'.
            '(?:#'.self::FRAGMENT.')?'.
        ')';

    /**
     * [RFC3986](https://tools.ietf.org/html/rfc3986#section-4.1):
     *
     * ```
     * URI-reference = URI / relative-ref
     * ```
     *
     * Captures:
     *
     * - ``uri_reference``,
     *      - ``uri``,
     *           - ``scheme``,
     *           - ``hier_part``,
     *                - ``authority``,
     *                    - ``userinfo``,
     *                    - ``host``,
     *                        - ``ip_literal``,
     *                            - ``ipv6address``,
     *                                - ``ls32``,
     *                                    - ``ipv6v4address``,
     *                            - ``ipvfuture``,
     *                        - ``ipv4address``,
     *                        - ``reg_name``,
     *                - ``path_abempty``,
     *                - ``path_absolute``,
     *                - ``path_rootless``,
     *                - ``path_empty``.
     *           - ``query``,
     *           - ``fragment``.
     *      - ``relative_ref``,
     *           - ``relative_part``,
     *                - ``authority``,
     *                    - ``userinfo``,
     *                    - ``host``,
     *                        - ``ip_literal``,
     *                            - ``ipv6address``,
     *                                - ``ls32``,
     *                                    - ``ipv6v4address``,
     *                            - ``ipvfuture``,
     *                        - ``ipv4address``,
     *                        - ``reg_name``,
     *                - ``path_abempty``,
     *                - ``path_absolute``,
     *                - ``path_noscheme``,
     *                - ``path_empty``.
     *           - ``query``,
     *           - ``fragment``.
     */
    public const URI_REFERENCE =
        '(?<uri_reference>(?J)'.
            self::URI.
            '|'.
            self::RELATIVE_REF.
        ')';

    /**
     * RFC3986 rule names.
     */
    protected static $rfc3986Rules = [
        'ALPHACHARS',
        'DIGITCHARS',
        'HEXDIGCHARS',
        'GEN_DELIM_CHARS',
        'SUB_DELIM_CHARS',
        'RESERVEDCHARS',
        'UNRESERVEDCHARS',
        'PCHARCHARS',
        'ALPHA',
        'DIGIT',
        'HEXDIG',
        'SUB_DELIMS',
        'GEN_DELIMS',
        'RESERVED',
        'UNRESERVED',
        'PCT_ENCODED',
        'PCHAR',
        'SEGMENT_NZ_NC',
        'SEGMENT_NZ',
        'SEGMENT',
        'PATH_EMPTY',
        'PATH_NOSCHEME',
        'PATH_ROOTLESS',
        'PATH_ABSOLUTE',
        'PATH_ABEMPTY',
        'REG_NAME',
        'DEC_OCTET',
        'DEC4OCTETS',
        'IPV4ADDRESS',
        'IPV6V4ADDRESS',
        'H16',
        'LS32',
        'IPV6ADDRESS',
        'IPVFUTURE',
        'IP_LITERAL',
        'PORT',
        'HOST',
        'USERINFO',
        'AUTHORITY',
        'SCHEME',
        'RELATIVE_PART',
        'HIER_PART',
        'FRAGMENT',
        'QUERY',
        'RELATIVE_REF',
        'ABSOLUTE_URI',
        'URI',
        'URI_REFERENCE',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getClassRuleNames() : array
    {
        return self::$rfc3986Rules;
    }
}

// vim: syntax=php sw=4 ts=4 et:
