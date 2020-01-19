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

use Korowai\Lib\Rfc\Rfc2849;
use Korowai\Lib\Rfc\Rfc2253;
use Korowai\Lib\Rfc\Rfc3986;
use Korowai\Lib\Rfc\Rfc5234;
use Korowai\Testing\Rfclib\TestCase;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Rfc2849Test extends TestCase
{
    public static function getRfcClass() : string
    {
        return Rfc2849::class;
    }

    public static function characterClasses__cases()
    {
        return [
            // character lists for character classes

            // character classes
            'ALPHA'             => [ Rfc2849::ALPHA,            Rfc5234::ALPHA ],
            'DIGIT'             => [ Rfc2849::DIGIT,            Rfc5234::DIGIT ],
            'CR'                => [ Rfc2849::CR,               Rfc5234::CR ],
            'LF'                => [ Rfc2849::LF,               Rfc5234::LF ],
            'SPACE'             => [ Rfc2849::SPACE,            Rfc5234::SP ],
            'ATTR_TYPE_CHARS'   => [ Rfc2849::ATTR_TYPE_CHARS,  '[0-9A-Za-z-]' ],
            'BASE64_CHAR'       => [ Rfc2849::BASE64_CHAR,      '[\+\/0-9=A-Za-z]' ],
            'OPT_CHAR'          => [ Rfc2849::OPT_CHAR,         Rfc2849::ATTR_TYPE_CHARS ],
            'SAFE_INIT_CHAR'    => [ Rfc2849::SAFE_INIT_CHAR,   '[\x01-\x09\x0B-\x0C\x0E-\x1F\x21-\x39\x3B\x3D-\x7F]' ],
            'SAFE_CHAR'         => [ Rfc2849::SAFE_CHAR,        '[\x01-\x09\x0B-\x0C\x0E-\x7F]' ],
            'SEP'               => [ Rfc2849::SEP,              '(?:'.Rfc2849::CR.Rfc2849::LF.'|'.Rfc2849::LF.')' ],
            'EOL'               => [ Rfc2849::EOL,              '(?:'.Rfc2849::SEP.'|$)' ],
            'NOTEOL'            => [ Rfc2849::NOTEOL,           '(?:[^'.Rfc2849::CR.Rfc2849::LF.']|'.Rfc2849::CR.'(?!'.Rfc2849::LF.'))' ],
        ];
    }

    /**
     * @dataProvider characterClasses__cases
     */
    public function test__characterClasses(string $actual, string $expect)
    {
        $this->assertSame($expect, $actual);
    }

    public static function simpleProductions__cases()
    {
        return [
            'SEP'                       => [ Rfc2849::SEP,                      '(?:'.Rfc2849::CR.Rfc2849::LF.'|'.Rfc2849::LF.')' ],
            'EOL'                       => [ Rfc2849::EOL,                      '(?:'.Rfc2849::SEP.'|$)' ],
            'NOTEOL'                    => [ Rfc2849::NOTEOL,                   '(?:[^'.Rfc2849::CR.Rfc2849::LF.']|'.Rfc2849::CR.'(?!'.Rfc2849::LF.'))' ],
            'FILL'                      => [ Rfc2849::FILL,                     '(?:'.Rfc2849::SPACE.'*)' ],
            'VERSION_NUMBER'            => [ Rfc2849::VERSION_NUMBER,           '(?:'.Rfc2849::DIGIT.'+)' ],
            'BASE64_STRING'             => [ Rfc2849::BASE64_STRING,            '(?:'.Rfc2849::BASE64_CHAR.'*)' ],
            'BASE64_UTF8_STRING'        => [ Rfc2849::BASE64_UTF8_STRING,       Rfc2849::BASE64_STRING ],
            'SAFE_STRING'               => [ Rfc2849::SAFE_STRING,              '(?:(?:'.Rfc2849::SAFE_INIT_CHAR.Rfc2849::SAFE_CHAR.'*)?)' ],
            'LDAP_OID'                  => [ Rfc2849::LDAP_OID,                 Rfc2253::OID ],
            'OPTION'                    => [ Rfc2849::OPTION,                   '(?:'.Rfc2849::OPT_CHAR.'+)' ],
            'OPTIONS'                   => [ Rfc2849::OPTIONS,                  '(?:'.Rfc2849::OPTION.'(?:;'.Rfc2849::OPTION.')*)' ],
            'ATTRIBUTE_TYPE'            => [ Rfc2849::ATTRIBUTE_TYPE,           '(?:'.Rfc2849::LDAP_OID.'|(?:'.Rfc2849::ALPHA.Rfc2849::ATTR_TYPE_CHARS.'*))' ],
            'ATTRIBUTE_DESCRIPTION'     => [ Rfc2849::ATTRIBUTE_DESCRIPTION,    '(?:'.Rfc2849::ATTRIBUTE_TYPE.'(?:;'.Rfc2849::OPTIONS.')?)' ],
            'DISTINGUISHED_NAME'        => [ Rfc2849::DISTINGUISHED_NAME,       Rfc2849::SAFE_STRING ],
            'BASE64_DISTINGUISHED_NAME' => [ Rfc2849::BASE64_DISTINGUISHED_NAME,Rfc2849::BASE64_UTF8_STRING ],
            'RDN'                       => [ Rfc2849::RDN,                      Rfc2849::SAFE_STRING ],
            'BASE64_RDN'                => [ Rfc2849::BASE64_RDN,               Rfc2849::BASE64_UTF8_STRING ],
            'URL'                       => [ Rfc2849::URL,                      Rfc3986::URI_REFERENCE ],
            'ATTRVAL_SPEC'              => [ Rfc2849::ATTRVAL_SPEC,             '(?:(?<attr_desc>'.Rfc2849::ATTRIBUTE_DESCRIPTION.')'.Rfc2849::VALUE_SPEC.Rfc2849::EOL.')' ],
        ];
    }

    /**
     * @dataProvider simpleProductions__cases
     */
    public function test__simpleProductions(string $actual, string $expect)
    {
        $this->assertSame($expect, $actual);
    }

    //
    // VERSION_NUMBER
    //

    public static function VERSION_NUMBER__cases()
    {
        return [
            '1'     => ['1',       [0 => ['1', 0]]],
            '0123'  => ['0123',    [0 => ['0123', 0]]],
        ];
    }

    public static function non__VERSION_NUMBER__cases()
    {
        $strings = ['', 'a', '1F'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider VERSION_NUMBER__cases
     */
    public function test__VERSION_NUMBER__matches(string $string, array $pieces)
    {
        $this->assertRfcMatches($string, 'VERSION_NUMBER', $pieces);
    }

    /**
     * @dataProvider non__VERSION_NUMBER__cases
     */
    public function test__VERSION_NUMBER__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'VERSION_NUMBER');
    }

    //
    // VERSION_SPEC
    //

    public static function VERSION_SPEC__cases()
    {
        return [
            'version:   0123' => [
                //           0000000000111111
                //           0123456789012345
                'string' => 'version:   0123',
                'pieces' => [
                    0 => ['version:   0123', 0],
                    'version_number' => ['0123', 11],
                    'version_error' => false,
                ],
            ],
            'version: 0123\n' => [
                //           0000000000111111
                //           0123456789012345
                'string' => "version: 0123\n",
                'pieces' => [
                    0 => ['version: 0123', 0],
                    'version_number' => ['0123', 9],
                    'version_error' => false,
                ],
            ],
            'version: ' => [
                //           0000000000111111
                //           0123456789012345
                'string' => "version: ",
                'pieces' => [
                    0 => ['version: ', 0],
                    'version_number' => false,
                    'version_error' => ['', 9],
                ],
            ],
            'version: foo' => [
                //           0000000000111111
                //           0123456789012345
                'string' => "version: foo",
                'pieces' => [
                    0 => ['version: foo', 0],
                    'version_number' => false,
                    'version_error' => ['foo', 9],
                ],
            ],
        ];
    }

    public static function non__VERSION_SPEC__cases()
    {
        $strings = ['', 'a', 'dn:123', 'a', '1F'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider VERSION_SPEC__cases
     */
    public function test__VERSION_SPEC__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'VERSION_SPEC', $pieces, $options);
    }

    /**
     * @dataProvider non__VERSION_SPEC__cases
     */
    public function test__VERSION_SPEC__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'VERSION_SPEC');
    }

    //
    // BASE64_STRING
    //

    public static function BASE64_STRING__cases()
    {
        $strings = ['', 'azAZ09+/=='];
        return static::stringsToPregTuples($strings);
    }

    public static function non__BASE64_STRING__cases()
    {
        $strings = ['?', '-', ' ', 'azAZ09+/==?'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider BASE64_STRING__cases
     */
    public function test__BASE64_STRING__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'BASE64_STRING', $pieces);
    }

    /**
     * @dataProvider non__BASE64_STRING__cases
     */
    public function test__BASE64_STRING__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'BASE64_STRING');
    }

    //
    // SAFE_STRING
    //

    public static function SAFE_STRING__cases()
    {
        $strings = ['', "\x01", "\x7F", 'a', "a ", "a:", "a<"];
        return static::stringsToPregTuples($strings);
    }

    public static function non__SAFE_STRING__cases()
    {
        $strings = ["\0", "\n", "\r", "\x80", "\xAA", " ", ":", "<", 'ł', 'tył', "a\0", "a\n", "a\r", "a\x80"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider SAFE_STRING__cases
     */
    public function test__SAFE_STRING__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'SAFE_STRING', $pieces);
    }

    /**
     * @dataProvider non__SAFE_STRING__cases
     */
    public function test__SAFE_STRING__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'SAFE_STRING');
    }

    //
    // LDAP_OID
    //

    public static function LDAP_OID__cases()
    {
        return Rfc2253Test::OID__cases();
    }

    public static function non__LDAP_OID__cases()
    {
        return Rfc2253Test::non__OID__cases();
    }

    /**
     * @dataProvider LDAP_OID__cases
     */
    public function test__LDAP_OID__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'LDAP_OID', $pieces);
    }

    /**
     * @dataProvider non__LDAP_OID__cases
     */
    public function test__LDAP_OID__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'LDAP_OID');
    }

    //
    // OPTION
    //

    public static function OPTION__cases()
    {
        $strings = ['a', '-', 'ab1-', '--'];
        return static::stringsToPregTuples($strings);
    }

    public static function non__OPTION__cases()
    {
        $strings = ['', '?', 'ab1-?'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider OPTION__cases
     */
    public function test__OPTION__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'OPTION', $pieces);
    }

    /**
     * @dataProvider non__OPTION__cases
     */
    public function test__OPTION__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'OPTION');
    }

    //
    // OPTIONS
    //

    public static function OPTIONS__cases()
    {
        $strings = ['a', '-', 'ab1-', '--', 'ab1-;cd2-4'];
        return static::stringsToPregTuples($strings);
    }

    public static function non__OPTIONS__cases()
    {
        $strings = ['', '?', 'ab1-?', 'ab1-;cd2-?'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider OPTIONS__cases
     */
    public function test__OPTIONS__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'OPTIONS', $pieces);
    }

    /**
     * @dataProvider non__OPTIONS__cases
     */
    public function test__OPTIONS__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'OPTIONS');
    }

    //
    // ATTRIBUTE_TYPE
    //

    public static function ATTRIBUTE_TYPE__cases()
    {
        $strings = ['a', 'a-'];
        return array_merge(
            static::LDAP_OID__cases(),
            static::stringsToPregTuples($strings)
        );
    }

    public static function non__ATTRIBUTE_TYPE__cases()
    {
        $strings = ['', '?', '-', '-a', 'ab1-?', '1.', '.1', 'a.b'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider ATTRIBUTE_TYPE__cases
     */
    public function test__ATTRIBUTE_TYPE__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'ATTRIBUTE_TYPE', $pieces);
    }

    /**
     * @dataProvider non__ATTRIBUTE_TYPE__cases
     */
    public function test__ATTRIBUTE_TYPE__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'ATTRIBUTE_TYPE');
    }

    //
    // ATTRIBUTE_DESCRIPTION
    //

    public static function ATTRIBUTE_DESCRIPTION__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::ATTRIBUTE_TYPE__cases() as $attrType) {
            $inheritedCases[] = $attrType;
            foreach (static::OPTIONS__cases() as $options) {
                $inheritedCases[] = static::joinPregTuples([$attrType, $options], ['glue' => ';']);
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__ATTRIBUTE_DESCRIPTION__cases()
    {
        $strings = ['', '?', '-', '-a', 'ab1-?', '1.', '.1', 'a.b'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider ATTRIBUTE_DESCRIPTION__cases
     */
    public function test__ATTRIBUTE_DESCRIPTION__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'ATTRIBUTE_DESCRIPTION', $pieces);
    }

    /**
     * @dataProvider non__ATTRIBUTE_DESCRIPTION__cases
     */
    public function test__ATTRIBUTE_DESCRIPTION__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'ATTRIBUTE_DESCRIPTION');
    }

    //
    // DISTINGUISHED_NAME
    //

    public static function DISTINGUISHED_NAME__cases()
    {
        return static::SAFE_STRING__cases();
    }

    public static function non__DISTINGUISHED_NAME__cases()
    {
        return static::non__SAFE_STRING__cases();
    }

    /**
     * @dataProvider DISTINGUISHED_NAME__cases
     */
    public function test__DISTINGUISHED_NAME__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'DISTINGUISHED_NAME', $pieces);
    }

    /**
     * @dataProvider non__DISTINGUISHED_NAME__cases
     */
    public function test__DISTINGUISHED_NAME__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'DISTINGUISHED_NAME');
    }

    //
    // BASE64_DISTINGUISHED_NAME
    //

    public static function BASE64_DISTINGUISHED_NAME__cases()
    {
        return static::BASE64_STRING__cases();
    }

    public static function non__BASE64_DISTINGUISHED_NAME__cases()
    {
        return static::non__BASE64_STRING__cases();
    }

    /**
     * @dataProvider BASE64_DISTINGUISHED_NAME__cases
     */
    public function test__BASE64_DISTINGUISHED_NAME__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'BASE64_DISTINGUISHED_NAME', $pieces);
    }

    /**
     * @dataProvider non__BASE64_DISTINGUISHED_NAME__cases
     */
    public function test__BASE64_DISTINGUISHED_NAME__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'BASE64_DISTINGUISHED_NAME');
    }

    //
    // DN_SPEC
    //

    public static function DN_SPEC__cases()
    {
        return [
            'dn: ' => [
                'string' => "dn: ",
                'pieces' => [
                    0 => ["dn: ", 0],
                    'value_safe' => ['', strlen('dn: ')],
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ],
            ],

            'dn: \nnext' => [
                'string' => "dn: \nnext",
                'pieces' => [
                    0 => ["dn: ", 0],
                    'value_safe' => ['', strlen('dn: ')],
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ],
            ],

            'dn: dc=example,dc=org' => [
                'string' => "dn: dc=example,dc=org",
                'pieces' => [
                    0 => ["dn: dc=example,dc=org", 0],
                    'value_safe' => ['dc=example,dc=org', strlen('dn: ')],
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ],
            ],

            'dn: dc=example,dc=org\nnext' => [
                'string' => "dn: dc=example,dc=org\nnext",
                'pieces' => [
                    0 => ["dn: dc=example,dc=org", 0],
                    'value_safe' => ['dc=example,dc=org', strlen('dn: ')],
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ],
            ],

            'dn: :unsafe\nnext' => [
                'string' => "dn: :unsafe\nnext",
                'pieces' => [
                    0 => ["dn: :unsafe", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => [':unsafe', strlen('dn: ')],
                    'value_b64_error' => false,
                ],
            ],

            'dn: błąd\nnext' => [
                'string' => "dn: błąd\nnext",
                'pieces' => [
                    0 => ["dn: błąd", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => ['łąd', strlen('dn: b')],
                    'value_b64_error' => false,
                ],
            ],

            'dn:: ' => [
                'string' => "dn:: ",
                'pieces' => [
                    0 => ["dn:: ", 0],
                    'value_safe' => false,
                    'value_b64' => ['', strlen('dn:: ')],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ],
            ],

            'dn:: \nnext' => [
                'string' => "dn:: \nnext",
                'pieces' => [
                    0 => ["dn:: ", 0],
                    'value_safe' => false,
                    'value_b64' => ['', strlen('dn:: ')],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ],
            ],

            'dn:: ASDF==\nnext' => [
                'string' => "dn:: ASDF==\nnext",
                'pieces' => [
                    0 => ["dn:: ASDF==", 0],
                    'value_safe' => false,
                    'value_b64' => ['ASDF==', strlen('dn:: ')],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ],
            ],

            'dn:: błąd\nnext' => [
                'string' => "dn:: błąd==\nnext",
                'pieces' => [
                    0 => ["dn:: błąd==", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => ['łąd==', strlen('dn:: b')],
                ],
            ],
        ];
    }

    public static function non__DN_SPEC__cases()
    {
        $strings = ['', 'a', 'xyz:'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider DN_SPEC__cases
     */
    public function test__DN_SPEC__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'DN_SPEC', $pieces, $options);
    }

    /**
     * @dataProvider non__DN_SPEC__cases
     */
    public function test__DN_SPEC__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'DN_SPEC');
    }

    //
    // URL
    //

    public static function URL__cases()
    {
        return [
            // #0
            [
                '',
                [
                    'uri_reference'     => ['', 0],
                    'uri'               => false,
                    'scheme'            => false,
                    'authority'         => false,
                    'host'              => false,
                    'path_abempty'      => false,
                    'path_absolute'     => false,
                    'path_noscheme'     => false,
                    'path_rootless'     => false,
                    'path_empty'        => ['', 0],
                    'relative_ref'      => ['', 0],
                ],
            ],
            // #1
            [
                '/',
                [
                    'uri_reference'     => ['/', 0],
                    'uri'               => false,
                    'scheme'            => false,
                    'authority'         => false,
                    'host'              => false,
                    'path_abempty'      => false,
                    'path_absolute'     => ['/', 0],
                    'path_noscheme'     => false,
                    'path_rootless'     => false,
                    'path_empty'        => false,
                    'relative_ref'      => ['/', 0]
                ],
            ],
            // #2
            [
            //   012345678
                'a.b-c+d:',
                [
                    'uri_reference'     => ['a.b-c+d:', 0],
                    'uri'               => ['a.b-c+d:', 0],
                    'scheme'            => ['a.b-c+d', 0],
                    'authority'         => false,
                    'host'              => false,
                    'path_abempty'      => false,
                    'path_absolute'     => false,
                    'path_noscheme'     => false,
                    'path_rootless'     => false,
                    'path_empty'        => ['', 8],
                    'relative_ref'      => false
                ],
            ],
            // #3
            [
            //   00000000001
            //   01234567890
                'a.b-c+d:xxx',
                [
                    'uri_reference'     => ['a.b-c+d:xxx', 0],
                    'uri'               => ['a.b-c+d:xxx', 0],
                    'authority'         => false,
                    'host'              => false,
                    'path_abempty'      => false,
                    'path_absolute'     => false,
                    'path_noscheme'     => false,
                    'path_rootless'     => ['xxx', 8],
                    'path_empty'        => false,
                    'relative_ref'      => false
                ],
            ],
            // #4
            [
            //   0000000000111
            //   0123456789012
                'a.b-c+d:/xxx',
                [
                    'uri_reference'     => ['a.b-c+d:/xxx', 0],
                    'uri'               => ['a.b-c+d:/xxx', 0],
                    'authority'         => false,
                    'host'              => false,
                    'path_abempty'      => false,
                    'path_absolute'     => ['/xxx', 8],
                    'path_noscheme'     => false,
                    'path_rootless'     => false,
                    'path_empty'        => false,
                    'relative_ref'      => false
                ],
            ],
            // #5
            [
            //   0000000000111111111122
            //   0123456789012345678901
                'a.b-c+d://example.com',
                [
                    'uri_reference'     => ['a.b-c+d://example.com', 0],
                    'uri'               => ['a.b-c+d://example.com', 0],
                    'authority'         => ['example.com', 10],
                    'host'              => ['example.com', 10],
                    'path_abempty'      => ['', 21],
                    'path_absolute'     => false,
                    'path_noscheme'     => false,
                    'path_rootless'     => false,
                    'path_empty'        => false,
                    'relative_ref'      => false
                ],
            ],
            // #6
            [
            //   00000000001111111111222222222233333333334444
            //   01234567890123456789012345678901234567890123
                'a.b-c+d://jsmith@example.com/foo?a=v#fr?b=w',
                [
                    'uri_reference'     => ['a.b-c+d://jsmith@example.com/foo?a=v#fr?b=w', 0],
                    'uri'               => ['a.b-c+d://jsmith@example.com/foo?a=v#fr?b=w', 0],
                    'authority'         => ['jsmith@example.com', 10],
                    'userinfo'          => ['jsmith', 10],
                    'host'              => ['example.com', 17],
                    'path_abempty'      => ['/foo', 28],
                    'path_absolute'     => false,
                    'path_noscheme'     => false,
                    'path_rootless'     => false,
                    'path_empty'        => false,
                    'query'             => ['a=v', 33],
                    'fragment'          => ['fr?b=w', 37],
                    'relative_ref'      => false
                ],
            ],
        ];
    }

    public static function non__URL__cases()
    {
        $strings = [':', '%', '%1'];
        $inheritedCases = [];
        foreach (static::non__SAFE_STRING__cases() as $nonStr) {
            if (!preg_match('/^ /', $nonStr[0])) {
                $inheritedCases[] = [': '.$nonStr[0]];
            }
        }
        foreach (static::non__BASE64_STRING__cases() as $nonB64Str) {
            if (!preg_match('/^ /', $nonB64Str[0])) {
                $inheritedCases[] = [':: '.$nonB64Str[0]];
            }
        }
        return array_merge($inheritedCases, static::stringsToPregTuples($strings));
    }

    /**
     * @dataProvider URL__cases
     */
    public function test__URL__matches(string $string, array $pieces)
    {
        $this->assertRfcMatches($string, 'URL', $pieces);
    }

    /**
     * @dataProvider non__URL__cases
     */
    public function test__URL__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'URL');
    }

    //
    // VALUE_SPEC
    //

    public static function VALUE_SPEC__cases()
    {
        $cases = [
            ':\n' => [
                //           0000
                //           0123
                'string' => ":\n",
                'pieces' => [
                    0 => [":", 0],
                    'value_safe' => ['', 1],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            '::\n' => [
                //   00000
                //   01234
                'string' => "::\n",
                'pieces' => [
                    0 => ["::", 0],
                    'value_safe' => false,
                    'value_b64' => ['', 2],
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ':<\n' => [
                //           00 00
                //           01 23
                'string' => ":<\n",
                'pieces' => [
                    0 => [":<", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => ['', 2],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ':</\n' => [
                //           000 00
                //           012 34
                'string' => ":</\n",
                'pieces' => [
                    0 => [":</", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => ['/', 2],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ':<file:/\n' => [
                //   00000000 00
                //   01234567 89
                'string' => ":<file:/\n",
                'pieces' => [
                    0 => [":<file:/", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => ['file:/', 2],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ':<#\n' => [
            //   000 00
            //   012 34
                'string' => ":<#\n",
                'pieces' => [
                    0 => [":<#", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => ['#', 2],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ': :foo' => [
                //           0000000
                //           0123456
                'string' => ": :foo",
                'pieces' => [
                    0 => [": :foo", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => [':foo', 2],
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ': łuszcz\n' => [
                //           00000000 00
                //           01234567 89
                'string' => ": łuszcz\n",
                'pieces' => [
                    0 => [": łuszcz", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => ['łuszcz', 2],
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ': tłuszcz\n' => [
                //           000000000 01
                //           012345678 90
                'string' => ": tłuszcz\n",
                'pieces' => [
//                    0 => [": tłuszcz", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => ['łuszcz', 3],
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ],
            ],
            ':::foo' => [
                //           0000000
                //           0123456
                'string' => ":::foo",
                'pieces' => [
                    0 => [":::foo", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => [':foo', 2],
                    'value_url_error' => false,
                ],
            ],
            ':: :foo' => [
                //           00000000
                //           01234567
                'string' => ":: :foo",
                [
                    0 => [":: :foo", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => [':foo', 3],
                    'value_url_error' => false,
                ],
            ],
            ':: A1@x=+\n' => [
                //           000000000 01
                //           012345678 90
                'string' => ":: A1@x=+\n",
                'pieces' => [
                    0 => [":: A1@x=+", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => ['@x=+', 5],
                    'value_url_error' => false,
                ],
            ],
            ':<# \n' => [
                //           0000 00
                //           0123 45
                'string' => ":<# \n",
                'pieces' => [
                    0 => [":<# ", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => [' ', 3],
                ],
            ],
            ':<##  xx\n' => [
                //   00000000 00
                //   01234567 89
                'string' => ":<##  xx\n",
                'pieces' => [
                    0 => [":<##  xx", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => ['#  xx', 3],
                ],
            ],
            ':<http://with spaces/\n' => [
                //           000000000011111111122 2
                //           012345678901234567890 1
                'string' => ":<http://with spaces/\n",
                'pieces' => [
                    0 => [":<http://with spaces/", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => [' spaces/', 13],
                ],
            ],
        ];

        $inheritedCases = [];
        foreach (static::SAFE_STRING__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => ': ',
                'merge' => [
                    'value_safe' => [$case[0], strlen(': ')],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false
                ]
            ]);
        }
        foreach (static::BASE64_STRING__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => ':: ',
                'merge' => [
                    'value_safe' => false,
                    'value_b64' => [$case[0], strlen(':: ')],
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false
                ]
            ]);
        }
        foreach (static::URL__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => ':< ',
                'merge' => [
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => [$case[0], strlen(':< ')],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false
                ]
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__VALUE_SPEC__cases()
    {
        $strings = ['', 'a', 'xyz:123', 'a', '1F'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider VALUE_SPEC__cases
     */
    public function test__VALUE_SPEC__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'VALUE_SPEC', $pieces, $options);
    }

    /**
     * @dataProvider non__VALUE_SPEC__cases
     */
    public function test__VALUE_SPEC__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'VALUE_SPEC');
    }

    //
    // CONTROL
    //

    public static function CONTROL__cases()
    {
        $cases = [
            [
            //   012345678
                "control:",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => ['', 8],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0123456789
                "control: ",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => ['', 9],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0000000000011
            //   0123456789012
                "control: #$%",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => ['#$%', 9],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0000000000011
            //   0123456789012
                "control: :",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => [':', 9],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   000000000001111
            //   012345678901234
                "control: :asdf",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => [':asdf', 9],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0000000000011
            //   0123456789012
                "control: 1.2.",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => ['.', 12],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0000000000011
            //   0123456789012
                "control: 1.2. ",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => ['. ', 12],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0000000000011111111
            //   0123456789012345678
                "control: 1.2. true",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => ['. true', 12],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0000000000011
            //   0123456789012
                "control: 1.2. ",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => ['. ', 12],
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   0000000000011111
            //   0123456789012345
                "control: 1.2.33",
                [
                    'ctl_type' => ['1.2.33', 9],
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   00000000000111111
            //   01234567890123456
                "control: 1.2.33 ",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => ['', 16],
                ]
            ],
            [
            //   00000000000111111111
            //   01234567890123456789
                "control: 1.2.33 true",
                [
                    'ctl_type' => ['1.2.33', 9],
                    'ctl_crit' => ['true', 16],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   00000000000111111111
            //   01234567890123456789
                "control: 1.2.33 false",
                [
                    'ctl_type' => ['1.2.33', 9],
                    'ctl_crit' => ['false', 16],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   00000000000111111111
            //   01234567890123456789
                "control: 1.2.33 xyz",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => ['xyz', 16],
                ]
            ],
            [
            //   000000000001111111112222
            //   012345678901234567890123
                "control: 1.2.33 truexyz",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => ['truexyz', 16],
                ]
            ],
            [
            //   000000000001111111112222
            //   012345678901234567890123
                "control: 1.2.33 falsexyz",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => ['falsexyz', 16],
                ]
            ],
            [
            //   000000000001111111112222
            //   012345678901234567890123
                "control: 1.2.33 :asdf",
                [
                    'ctl_type' => false,
                    'ctl_crit' => false,
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => [':asdf', 16],
                ]
            ],
            [
            //   000000000001111111112222
            //   012345678901234567890123
                "control: 1.2.33: asdf",
                [
                    'ctl_type' => ['1.2.33', 9],
                    'ctl_crit' => false,
                    'value_safe' => ['asdf', 17],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => false,
                ]
            ],
            [
            //   000000000001111111112222222
            //   012345678901234567890123456
                "control: 1.2.33 true: asdf",
                [
                    'ctl_type' => ['1.2.33', 9],
                    'ctl_crit' => ['true', 16],
                    'value_safe' => ['asdf', 22],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                    'ctl_type_error' => false,
                    'ctl_crit_error' => false,
                ]
            ],
        ];

        $inheritedCases = [];
        foreach (static::VALUE_SPEC__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => 'control: 1.23',
                'suffix' => "\n",
                'prefixMain' => true,
                'suffixMain' => true,
                'merge' => [
                    'ctl_type'  => ['1.23', 9],
                    'ctl_crit'  => false,
                ]
            ]);
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => 'control: 1.23 true',
                'suffix' => "\n",
                'prefixMain' => true,
                'suffixMain' => true,
                'merge' => [
                    'ctl_type'  => ['1.23', 9],
                    'ctl_crit'  => ['true', 14],
                ]
            ]);
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => 'control: 1.23 false',
                'suffix' => "\n",
                'prefixMain' => true,
                'suffixMain' => true,
                'merge' => [
                    'ctl_type'  => ['1.23', 9],
                    'ctl_crit'  => ['false', 14],
                ]
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__CONTROL__cases()
    {
        $strings = [
            '<:', '< %', '< %1', ':: %$', ': ł',
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider CONTROL__cases
     */
    public function test__CONTROL__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'CONTROL', $pieces, $options);
    }

    /**
     * @dataProvider non__CONTROL__cases
     */
    public function test__CONTROL__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'CONTROL');
    }

    //
    // ATTRVAL_SPEC
    //

    public static function ATTRVAL_SPEC__cases()
    {
        $cases = [
            [
            //   00000000001 111
            //   01234567890 123
                "ou;lang-pl:\nnext",
                [
                    0 => ["ou;lang-pl:\n", 0],
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_safe' => ['', 11],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   00000000001 1 11
            //   01234567890 1 23
                "ou;lang-pl:\r\nnext",
                [
                    0 => ["ou;lang-pl:\r\n", 0],
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_safe' => ['', 11],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   000000000011 11
            //   012345678901 23
                "ou;lang-pl::\nnext",
                [
                    0 => ["ou;lang-pl::\n", 0],
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_safe' => false,
                    'value_b64' => ['', 12],
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   000000000011 11
            //   012345678901 23
                "ou;lang-pl::\r\nnext",
                [
                    0 => ["ou;lang-pl::\r\n", 0],
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_safe' => false,
                    'value_b64' => ['', 12],
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   000000000011 11
            //   012345678901 23
                "ou;lang-pl:<\nnext",
                [
                    0 => ["ou;lang-pl:<\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => ['', 12],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   000000000011 11
            //   012345678901 23
                "ou;lang-pl:<\r\nnext",
                [
                    0 => ["ou;lang-pl:<\r\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => ['', 12],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   0000000000111 11
            //   0123456789012 34
                "ou;lang-pl:</\nnext",
                [
                    0 => ["ou;lang-pl:</\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => ['/', 12],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   0000000000111 11
            //   0123456789012 34
                "ou;lang-pl:</\r\nnext",
                [
                    0 => ["ou;lang-pl:</\r\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => ['/', 12],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   000000000011111111 1
            //   012345678901234567 8
                "ou;lang-pl:<file:/\nnext",
                [
                    0 => ["ou;lang-pl:<file:/\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => ['file:/', 12],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   0000000000111 1
            //   0123456789012 3
                "ou;lang-pl:<#\n",
                [
                    0 => ["ou;lang-pl:<#\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => ['#', 12],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   00000000001111111
            //   01234567890123456
                "ou;lang-pl: :foo",
                [
                    0 => ["ou;lang-pl: :foo", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => [':foo', 12],
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   000000000011111111 1
            //   012345678901245678 9
                "ou;lang-pl: łuszcz\nnext",
                [
                    0 => ["ou;lang-pl: łuszcz\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => ['łuszcz', 12],
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   0000000000111111111 1
            //   0123456789012345678 9
                "ou;lang-pl: tłuszcz\nnext",
                [
                    0 => ["ou;lang-pl: tłuszcz\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => ['łuszcz', 13],
                    'value_b64_error' => false,
                    'value_url_error' => false,
                ]
            ],
            [
            //   00000000001111111
            //   01234567890123456
                "ou;lang-pl:::foo",
                [
                    0 => ["ou;lang-pl:::foo", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => [':foo', 12],
                    'value_url_error' => false,
                ]
            ],
            [
            //   000000000011111111
            //   012345678901234567
                "ou;lang-pl:: :foo",
                [
                    0 => ["ou;lang-pl:: :foo", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => [':foo', 13],
                    'value_url_error' => false,
                ]
            ],
            [
            //   0000000000111111111 1
            //   0123456789012345678 9
                "ou;lang-pl:: A1@x=+\n",
                [
                    0 => ["ou;lang-pl:: A1@x=+\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => ['@x=+', 15],
                    'value_url_error' => false,
                ]
            ],
            [
            //   00000000001111 1
            //   01234567890123 4
                "ou;lang-pl:<# \n",
                [
                    0 => ["ou;lang-pl:<# \n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => [' ', 13],
                ]
            ],
            [
            //   000000000011111111 1
            //   012345678901234567 8
                "ou;lang-pl:<##  xx\n",
                [
                    0 => ["ou;lang-pl:<##  xx\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => ['#  xx', 13],
                ]
            ],
            [
            //   0000000000111111111122222222223 3
            //   0123456789012345678901234567890 1
                "ou;lang-pl:<http://with spaces/\n",
                [
                    0 => ["ou;lang-pl:<http://with spaces/\n", 0],
                    'value_safe' => false,
                    'attr_desc' => ['ou;lang-pl', 0],
                    'value_b64' => false,
                    'value_url' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                    'value_url_error' => [' spaces/', 23],
                ]
            ],
        ];
        $inheritedCases = [];
        foreach (static::ATTRIBUTE_DESCRIPTION__cases() as $attr) {
            [$_0a] = self::pregTupleKeys($attr, [0]);
            foreach (static::VALUE_SPEC__cases() as $value) {
                [$_0v] = self::pregTupleKeys($value, [0]);
                $joint = $attr[$_0a].$value[$_0v];
                $inheritedCases[$joint] = static::joinPregTuples([$attr, $value], [
                    'mergeMain' => [$joint, 0],
                ]);
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__ATTRVAL_SPEC__cases()
    {
        $strings = ['', 'a', ':123', 'a', '1F'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider ATTRVAL_SPEC__cases
     */
    public function test__ATTRVAL_SPEC__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'ATTRVAL_SPEC', $pieces, $options);
    }

    /**
     * @dataProvider non__ATTRVAL_SPEC__cases
     */
    public function test__ATTRVAL_SPEC__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'ATTRVAL_SPEC');
    }
//
//    //
//    // LDIF_ATTRVAL_RECORD
//    //
//
//    public static function LDIF_ATTRVAL_RECORD__cases()
//    {
//        $strings = [
//                "dn: \n".
//                "attr: \n",
//
//                "dn:: AAAFGFF==\n".
//                "attr-1: value1 - ?\n".
//                "attr-2:: SDAFDS/==\n".
//                "attr-:< file://\n",
//        ];
//        return static::stringsToPregTuples($strings);
//    }
//
//    public static function non__LDIF_ATTRVAL_RECORD__cases()
//    {
//        $strings = [
//            '',
//
//            "dn: \n",
//
//            "dn: \n".
//            "attr: ", // missing trailing \n
//        ];
//        return static::stringsToPregTuples($strings);
//    }
//
//    /**
//     * @dataProvider LDIF_ATTRVAL_RECORD__cases
//     */
//    public function test__LDIF_ATTRVAL_RECORD__matches(string $string, array $pieces = [])
//    {
//        $this->assertRfcMatches($string, 'LDIF_ATTRVAL_RECORD', $pieces);
//    }
//
//    /**
//     * @dataProvider non__LDIF_ATTRVAL_RECORD__cases
//     */
//    public function test__LDIF_ATTRVAL_RECORD__notMatches(string $string)
//    {
//        $this->assertRfcNotMatches($string, 'LDIF_ATTRVAL_RECORD');
//    }
//
    //
    // MOD_SPEC_INIT
    //

    public static function MOD_SPEC_INIT__cases()
    {
        $types = ['add', 'delete', 'replace'];

        $cases = [
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add:",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => ['', 4],
                    'attr_opts_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add:  ",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => ['', 6],
                    'attr_opts_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add:\next",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => ['', 4],
                    'attr_opts_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add: atłybut ",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => ['łybut ', 7],
                    'attr_opts_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add: atłybut \next",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => ['łybut ', 7],
                    'attr_opts_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add: cn;",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => false,
                    'attr_opts_error' => ['', 8],
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add: cn;a;",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => false,
                    'attr_opts_error' => [';', 9],
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add: cn;a;błąd",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => false,
                    'attr_opts_error' => ['łąd', 11],
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "add: cn;a;błąd\nnext",
                [
                    'mod_type' => ['add', 0],
                    'attr_desc' => false,
                    'attr_type_error' => false,
                    'attr_opts_error' => ['łąd', 11],
                ]
            ],
        ];

        $inheritedCases = [];

        foreach (Rfc2849Test::ATTRIBUTE_DESCRIPTION__cases() as $attr) {
            foreach ($types as $type) {
                $typeTuples = [$type, ['mod_type' => [$type, 0]]];
                $inheritedCases[] = static::joinPregTuples([$typeTuples, $attr], [
                    'glue' => ': ',
                    'merge' => [
                        'mod_type_error' => false,
                        'attr_opts_error' => false,
                        'attr_type_error' => false
                    ]
                ]);
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__MOD_SPEC_INIT__cases()
    {
        $strings = [];

        $inheritedCases = [];
        foreach (Rfc2849Test::ATTRIBUTE_DESCRIPTION__cases() as $attr) {
            $inheritedCases[] = ['foo: '.$attr[0]];
        }

        return array_merge($inheritedCases, static::stringsToPregTuples($strings));
    }

    /**
     * @dataProvider MOD_SPEC_INIT__cases
     */
    public function test__MOD_SPEC_INIT__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'MOD_SPEC_INIT', $pieces, $options);
    }

    /**
     * @dataProvider non__MOD_SPEC_INIT__cases
     */
    public function test__MOD_SPEC_INIT__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'MOD_SPEC_INIT');
    }

//
//    //
//    // CHANGERECORD_INIT
//    //
//
//    public static function CHANGERECORD_INIT__cases()
//    {
//        return [
//            [
//            //   000000000011111 11
//            //   012345678901234 56
//                "changetype: add\n",
//                ['chg_type' => ['add', 12]]
//            ],
//            [
//            //   000000000011111 11
//            //   012345678901234 56
//                "changetype: delete\n",
//                ['chg_type' => ['delete', 12]]
//            ],
//            [
//            //   000000000011111 11
//            //   012345678901234 56
//                "changetype: moddn\n",
//                ['chg_type' => ['moddn', 12]]
//            ],
//            [
//            //   000000000011111 11
//            //   012345678901234 56
//                "changetype: modrdn\n",
//                ['chg_type' => ['modrdn', 12]]
//            ],
//            [
//            //   000000000011111 11
//            //   012345678901234 56
//                "changetype: modify\n",
//                ['chg_type' => ['modify', 12]]
//            ],
//        ];
//    }
//
//    public static function non__CHANGERECORD_INIT__cases()
//    {
//        $strings = [
//            "",
//            "foo",
//            "changetype add",
//            "changetype:",
//            "changetype:\n",
//            "changetype: add", // missing \n
//            "changetype: foo\n",
//        ];
//
//        $inheritedCases = [];
//
//        return array_merge($inheritedCases, static::stringsToPregTuples($strings));
//    }
//
//    /**
//     * @dataProvider CHANGERECORD_INIT__cases
//     */
//    public function test__CHANGERECORD_INIT__matches(string $string, array $pieces = [])
//    {
//        $this->assertRfcMatches($string, 'CHANGERECORD_INIT', $pieces);
//    }
//
//    /**
//     * @dataProvider non__CHANGERECORD_INIT__cases
//     */
//    public function test__CHANGERECORD_INIT__notMatches(string $string)
//    {
//        $this->assertRfcNotMatches($string, 'CHANGERECORD_INIT');
//    }
    //
    // CHANGERECORD_INIT
    //

    public static function CHANGERECORD_INIT__cases()
    {
        $cases = [
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "changetype: add",
                [
                    'chg_type' => ['add', 12],
                    'chg_type_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "changetype: delete",
                [
                    'chg_type' => ['delete', 12],
                    'chg_type_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "changetype: moddn",
                [
                    'chg_type' => ['moddn', 12],
                    'chg_type_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "changetype: modrdn",
                [
                    'chg_type' => ['modrdn', 12],
                    'chg_type_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "changetype: modify",
                [
                    'chg_type' => ['modify', 12],
                    'chg_type_error' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "changetype: foo",
                [
                    'chg_type' => false,
                    'chg_type_error' => ['foo', 12],
                ]
            ],
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "changetype: ",
                [
                    'chg_type' => false,
                    'chg_type_error' => ['', 12],
                ]
            ],
            [
            //   000000000011 1111111122222222223
            //   012345678901 2345678901234567890
                "changetype: \n",
                [
                    'chg_type' => false,
                    'chg_type_error' => ['', 12],
                ]
            ],
        ];

        $inheritedCases = [];

        return array_merge($inheritedCases, $cases);
    }

    public static function non__CHANGERECORD_INIT__cases()
    {
        $strings = [
            "",
            "foo:",
            "changetype",
            "changetype foo\n",
            "changetype\n",
        ];

        $inheritedCases = [];

        return array_merge($inheritedCases, static::stringsToPregTuples($strings));
    }

    /**
     * @dataProvider CHANGERECORD_INIT__cases
     */
    public function test__CHANGERECORD_INIT__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'CHANGERECORD_INIT', $pieces, $options);
    }

    /**
     * @dataProvider non__CHANGERECORD_INIT__cases
     */
    public function test__CHANGERECORD_INIT__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'CHANGERECORD_INIT');
    }

    //
    // NEWRDN_SPEC
    //

    public static function NEWRDN_SPEC__cases()
    {
        $cases = [
            #0
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "newrdn: \nxx",
                [
                    ["newrdn: \n", 0],
                    'value_safe' => ['', 8],
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ]
            ],
            #1
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "newrdn:: \nxx",
                [
                    ["newrdn:: \n", 0],
                    'value_safe' => false,
                    'value_b64' => ['', 9],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ]
            ],
            #2
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "newrdn: błąd \nxx",
                [
                    ["newrdn: błąd \n", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => ['łąd ', 9],
                    'value_b64_error' => false,
                ]
            ],
            #3
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "newrdn:: błąd \nxx",
                [
                    ["newrdn:: błąd \n", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => ['łąd ', 10],
                ]
            ],
        ];

        $inheritedCases = [];

        return array_merge($inheritedCases, $cases);
    }

    public static function non__NEWRDN_SPEC__cases()
    {
        $strings = [
            "",
            "foo:",
            "newrdn",
            "newrdn foo\n",
        ];

        $inheritedCases = [];

        return array_merge($inheritedCases, static::stringsToPregTuples($strings));
    }

    /**
     * @dataProvider NEWRDN_SPEC__cases
     */
    public function test__NEWRDN_SPEC__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'NEWRDN_SPEC', $pieces, $options);
    }

    /**
     * @dataProvider non__NEWRDN_SPEC__cases
     */
    public function test__NEWRDN_SPEC__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'NEWRDN_SPEC');
    }

    //
    // NEWSUPERIOR_SPEC
    //

    public static function NEWSUPERIOR_SPEC__cases()
    {
        $cases = [
            'newsuperior:\n' => [
                //           000000000011 11
                //           012345678901 23
                'string' => "newsuperior:\n",
                'pieces' => [
                    0 => ["newsuperior:\n", 0],
                    'value_safe' => ['', 12],
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ]
            ],
            'newsuperior::\n' => [
                //           0000000000111 11
                //           0123456789012 34
                'string' => "newsuperior::\n",
                'pieces' => [
                    0 => ["newsuperior::\n", 0],
                    'value_safe' => false,
                    'value_b64' => ['', 13],
                    'value_safe_error' => false,
                    'value_b64_error' => false,
                ]
            ],
            'newsuperior: :foo' => [
                //           000000000011111111
                //           012345678901234567
                'string' => "newsuperior: :foo",
                'pieces' => [
                    0 => ["newsuperior: :foo", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => [':foo', 13],
                    'value_b64_error' => false,
                ]
            ],
            'newsuperior: łuszcz\nnext' => [
                //           0000000000111111111 222222
                //           0123456789012356789 012345
                'string' => "newsuperior: łuszcz\nnext",
                'pieces' => [
                    0 => ["newsuperior: łuszcz\n", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => ['łuszcz', 13],
                    'value_b64_error' => false,
                ]
            ],
            'newsuperior: tłuszcz\nnext' => [
                //           00000000001111111112 222222
                //           01234567890123467890 123456
                'string' => "newsuperior: tłuszcz\nnext",
                'pieces' => [
                    0 => ["newsuperior: tłuszcz\n", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => ['łuszcz', 14],
                    'value_b64_error' => false,
                ]
            ],
            'newsuperior:::foo' => [
                //           000000000011111111
                //           012345678901234567
                'string' => "newsuperior:::foo",
                'pieces' => [
                    0 => ["newsuperior:::foo", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => [':foo', 13],
                ]
            ],
            'newsuperior:: :foo' => [
                //           000000000011111111
                //           012345678901234567
                'string' => "newsuperior:: :foo",
                'pieces' => [
                    0 => ["newsuperior:: :foo", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => [':foo', 14],
                ]
            ],
            'newsuperior:: A1@x=+\n' => [
                //           00000000001111111111 22
                //           01234567890123456789 01
                'string' => "newsuperior:: A1@x=+\n",
                'pieces' => [
                    0 => ["newsuperior:: A1@x=+\n", 0],
                    'value_safe' => false,
                    'value_b64' => false,
                    'value_safe_error' => false,
                    'value_b64_error' => ['@x=+', 16],
                ]
            ],
        ];
        $inheritedCases = [];
        return array_merge($inheritedCases, $cases);
    }

    public static function non__NEWSUPERIOR_SPEC__cases()
    {
        $strings = ['', 'a', 'xyz:123', 'a', '1F'];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider NEWSUPERIOR_SPEC__cases
     */
    public function test__NEWSUPERIOR_SPEC__matches(string $string, array $pieces, array $options = ['suffix' => '/D'])
    {
        $this->assertRfcMatches($string, 'NEWSUPERIOR_SPEC', $pieces, $options);
    }

    /**
     * @dataProvider non__NEWSUPERIOR_SPEC__cases
     */
    public function test__NEWSUPERIOR_SPEC__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'NEWSUPERIOR_SPEC');
    }
}

// vim: syntax=php sw=4 ts=4 et:
