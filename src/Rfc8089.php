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
 * Syntax rules from [RFC8089](https://tools.ietf.org/html/rfc8089)
 * as PCRE regular expressions.
 *
 * **Example**:
 *
 * ```
 * $result = preg_match('/^'.Rfc8089::FILE_URI.'$/', $subject, $matches, PREG_UNMATCHED_AS_NULL)
 * ```
 */
class Rfc8089 extends Rfc3986
{
    /**
     * [RFC8089](https://tools.ietf.org/html/rfc8089#section-2):
     *
     * ```
     * file-auth = "localhost"
     *           / host
     * ```
     *
     * Captures:
     *
     * - ``file_auth``,
     *      - ``host``.
     *        - ``ip_literal``,
     *          - ``ipv6address``,
     *          - ``ls32``,
     *            - ``ipv6v4address``,
     *          - ``ipvfuture``,
     *        - ``ipv4address``,
     *        - ``reg_name``.
     */
    public const FILE_AUTH =
        '(?<file_auth>'.
            '(?:(?:localhost)|'.self::HOST.')'.
        ')';

    /**
     * [RFC8089](https://tools.ietf.org/html/rfc8089#section-2):
     *
     * ```
     * local-path = path-absolute
     * ```
     *
     * Captures:
     *
     * - ``local_path``,
     *      - ``path_absolute``.
     */
    public const LOCAL_PATH = '(?<local_path>'.self::PATH_ABSOLUTE.')';

    /**
     * [RFC8089](https://tools.ietf.org/html/rfc8089#section-2):
     *
     * ```
     * auth-path = [ file-auth ] path-absolute
     * ```
     *
     * Captures:
     *
     * - ``auth_path``,
     *      - ``file_auth``,
     *           - ``host``,
     *             - ``ip_literal``,
     *               - ``ipv6address``,
     *               - ``ls32``,
     *                 - ``ipv6v4address``,
     *               - ``ipvfuture``,
     *             - ``ipv4address``,
     *             - ``reg_name``,
     *      - ``path_absolute``.
     */
    public const AUTH_PATH =
        '(?<auth_path>'.
            self::FILE_AUTH.'?'.self::PATH_ABSOLUTE.
        ')';

    /**
     * [RFC8089](https://tools.ietf.org/html/rfc8089#section-2):
     *
     * ```
     * file-hier-part = ( "//" auth-path )
     *                / local-path
     * ```
     *
     * Captures:
     *
     * - ``file_hier_part``,
     *      - ``auth_path``,
     *          - ``file_auth``,
     *              - ``host``,
     *                  - ``ip_literal``,
     *                      - ``ipv6address``,
     *                      - ``ls32``,
     *                          - ``ipv6v4address``,
     *                      - ``ipvfuture``,
     *                  - ``ipv4address``,
     *                  - ``reg_name``,
     *          - ``path_absolute``,
     *      - ``local_path``,
     *          - ``path_absolute``.
     */
    public const FILE_HIER_PART =
        '(?J)(?<file_hier_part>'.
            '(?:(?:\/\/'.self::AUTH_PATH.')|'.self::LOCAL_PATH.')'.
        ')';

    /**
     * [RFC8089](https://tools.ietf.org/html/rfc8089#section-2):
     *
     * ```
     * file-scheme = "file"
     * ```
     *
     * Captures:
     *
     * - ``file_scheme``.
     */
    public const FILE_SCHEME = '(?<file_scheme>file)';

    /**
     * [RFC8089](https://tools.ietf.org/html/rfc8089#section-2):
     *
     * ```
     * file-URI = file-scheme ":" file-hier-part
     * ```
     *
     * Captures:
     *
     * - ``file_uri``,
     *      - ``file_scheme``,
     *      - ``file_hier_part``,
     *          - ``auth_path``,
     *              - ``file_auth``,
     *                  - ``host``,
     *                      - ``ip_literal``,
     *                          - ``ipv6address``,
     *                              - ``ls32``,
     *                                  - ``ipv6v4address``,
     *                              - ``ipvfuture``,
     *                          - ``ipv4address``,
     *                          - ``reg_name``,
     *              - ``path_absolute``,
     *           - ``local_path``,
     *              - ``path_absolute``.
     */
    public const FILE_URI =
        '(?<file_uri>'.
            self::FILE_SCHEME.':'.self::FILE_HIER_PART.
        ')';

    protected static $rfc8089Rules = [
        'FILE_AUTH',
        'LOCAL_PATH',
        'AUTH_PATH',
        'FILE_HIER_PART',
        'FILE_SCHEME',
        'FILE_URI',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getClassRuleNames() : array
    {
        return array_merge(self::$rfc8089Rules, parent::getClassRuleNames());
    }
}

// vim: syntax=php sw=4 ts=4 et:
