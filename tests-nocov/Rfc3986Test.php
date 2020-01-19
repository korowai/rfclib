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

use Korowai\Lib\Rfc\Rfc3986;
use Korowai\Lib\Rfc\Rfc5234;
use Korowai\Testing\Rfclib\TestCase;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Rfc3986Test extends TestCase
{
    public static function getRfcClass() : string
    {
        return Rfc3986::class;
    }

    //
    // ALPHA
    //
    public function test__ALPHA()
    {
        $this->assertSame(Rfc5234::ALPHACHARS, Rfc3986::ALPHACHARS);
        $this->assertSame(Rfc5234::ALPHA, Rfc3986::ALPHA);
    }

    //
    // DIGIT
    //
    public function test__DIGIT()
    {
        $this->assertSame(Rfc5234::DIGITCHARS, Rfc3986::DIGITCHARS);
        $this->assertSame(Rfc5234::DIGIT, Rfc3986::DIGIT);
    }


    //
    // HEXDIG
    //
    public function test__HEXDIG()
    {
        $this->assertSame('0-9A-Fa-f', Rfc3986::HEXDIGCHARS);
        $this->assertSame('[0-9A-Fa-f]', Rfc3986::HEXDIG);
    }


    //
    // SUB_DELIMS
    //
    public function test__SUB_DELIMS()
    {
        $this->assertSame('!\$&\'\(\)\*\+,;=', Rfc3986::SUB_DELIM_CHARS);
        $this->assertSame('[!\$&\'\(\)\*\+,;=]', Rfc3986::SUB_DELIMS);
    }

    //
    // GEN_DELIMS
    //

    public function test__GEN_DELIMS()
    {
        $this->assertSame(':\/\?#\[\]@', Rfc3986::GEN_DELIM_CHARS);
        $this->assertSame('[:\/\?#\[\]@]', Rfc3986::GEN_DELIMS);
    }

    //
    // RESERVED
    //

    public function test__RESERVED()
    {
        $this->assertSame(':\/\?#\[\]@!\$&\'\(\)\*\+,;=', Rfc3986::RESERVEDCHARS);
        $this->assertSame('[:\/\?#\[\]@!\$&\'\(\)\*\+,;=]', Rfc3986::RESERVED);
    }

    //
    // UNRESERVED
    //
    public function test__UNRESERVED()
    {
        $this->assertSame('A-Za-z0-9\._~-', Rfc3986::UNRESERVEDCHARS);
        $this->assertSame('[A-Za-z0-9\._~-]', Rfc3986::UNRESERVED);
    }

    //
    // PCT_ENCODED
    //
    public function test__PCT_ENCODED()
    {
        $this->assertSame('(?:%[0-9A-Fa-f][0-9A-Fa-f])', Rfc3986::PCT_ENCODED);
    }

    //
    // PCHAR
    //
    public function test__PCHAR()
    {
        $this->assertSame(':@!\$&\'\(\)\*\+,;=A-Za-z0-9\._~-', Rfc3986::PCHARCHARS);
        $this->assertSame('(?:[:@!\$&\'\(\)\*\+,;=A-Za-z0-9\._~-]|(?:%[0-9A-Fa-f][0-9A-Fa-f]))', Rfc3986::PCHAR);
    }

    //
    // SEGMENT_NZ_NC
    //

    public static function SEGMENT_NZ_NC__cases()
    {
        $strings = [
            "!$&'()*+,;=-._~Ab1%1fx",
        ];
        return static::stringsToPregTuples($strings);
    }

    public static function non__SEGMENT_NZ_NC__cases()
    {
        $strings = ["", ":", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "/", "?", "a/b", "a?"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider SEGMENT_NZ_NC__cases
     */
    public function test__SEGMENT_NZ_NC__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'SEGMENT_NZ_NC', $pieces);
    }

    /**
     * @dataProvider non__SEGMENT_NZ_NC__cases
     */
    public function test__SEGMENT_NZ_NC__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'SEGMENT_NZ_NC');
    }

    //
    // SEGMENT_NZ
    //

    public static function SEGMENT_NZ__cases()
    {
        $strings = [
            ":",
            ":!$&'()*+,;=-._~Ab1%1fx",
        ];
        return static::stringsToPregTuples($strings);
    }

    public static function non__SEGMENT_NZ__cases()
    {
        $strings = ["", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "/", "?", "a/b", "a?"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider SEGMENT_NZ__cases
     */
    public function test__SEGMENT_NZ__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'SEGMENT_NZ', $pieces);
    }

    /**
     * @dataProvider non__SEGMENT_NZ__cases
     */
    public function test__SEGMENT_NZ__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'SEGMENT_NZ');
    }

    //
    // SEGMENT
    //

    public static function SEGMENT__cases()
    {
        $strings = [
            "",
            ":",
            ":!$&'()*+,;=-._~Ab1%1fx",
        ];
        return static::stringsToPregTuples($strings);
    }

    public static function non__SEGMENT__cases()
    {
        $strings = ["%", "%1", "%G", "%1G", "%G2", "#", "ł", "/", "?", "a/b", "a?"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider SEGMENT__cases
     */
    public function test__SEGMENT__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'SEGMENT', $pieces);
    }

    /**
     * @dataProvider non__SEGMENT__cases
     */
    public function test__SEGMENT__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'SEGMENT');
    }

    //
    // PATH_EMPTY
    //
    public static function PATH_EMPTY__strings()
    {
        return [''];
    }

    public static function PATH_EMPTY__cases()
    {
        $strings = static::PATH_EMPTY__strings();
        return static::stringsToPregTuples($strings, 'path_empty');
    }

    public static function non__PATH_EMPTY__cases()
    {
        $strings = [ "a", "A", "1", "." ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider PATH_EMPTY__cases
     */
    public function test__PATH_EMPTY__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('path_empty', $pieces);
        $this->assertRfcMatches($string, 'PATH_EMPTY', $pieces);
    }

    /**
     * @dataProvider non__PATH_EMPTY__cases
     */
    public function test__PATH_EMPTY__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'PATH_EMPTY');
    }

    //
    // PATH_NOSCHEME
    //
    public static function PATH_NOSCHEME__strings()
    {
        return [
            "!$&'()*+,;=-._~Ab1%1fx",
            "!$&'()*+,;=-._~Ab1%1fx/",
            "!$&'()*+,;=-._~Ab1%1fx/:!$&'()*+,;=-._~Ab1%1fx",
        ];
    }

    public static function PATH_NOSCHEME__cases()
    {
        $strings = static::PATH_NOSCHEME__strings();
        return static::stringsToPregTuples($strings, 'path_noscheme');
    }

    public static function non__PATH_NOSCHEME__cases()
    {
        $strings = [":", ":/"];
        return array_merge(static::stringsToPregTuples($strings), static::non__PATH_ROOTLESS__cases());
    }

    /**
     * @dataProvider PATH_NOSCHEME__cases
     */
    public function test__PATH_NOSCHEME__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('path_noscheme', $pieces);
        $this->assertRfcMatches($string, 'PATH_NOSCHEME', $pieces);
    }

    /**
     * @dataProvider non__PATH_NOSCHEME__cases
     */
    public function test__PATH_NOSCHEME__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'PATH_NOSCHEME');
    }

    //
    // PATH_ROOTLESS
    //
    public static function PATH_ROOTLESS__strings()
    {
        $strings = [
            ":!$&'()*+,;=-._~Ab1%1fx",
            ":!$&'()*+,;=-._~Ab1%1fx/",
            ":!$&'()*+,;=-._~Ab1%1fx/:!$&'()*+,;=-._~Ab1%1fx",
        ];
        $inheritedStrings = static::PATH_NOSCHEME__strings();
        return array_merge($inheritedStrings, $strings);
    }

    public static function PATH_ROOTLESS__cases()
    {
        $strings = static::PATH_ROOTLESS__strings();
        return static::stringsToPregTuples($strings, 'path_rootless');
    }

    public static function non__PATH_ROOTLESS__cases()
    {
        $strings = ["", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "/", "?", "/a"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider PATH_ROOTLESS__cases
     */
    public function test__PATH_ROOTLESS__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('path_rootless', $pieces);
        $this->assertRfcMatches($string, 'PATH_ROOTLESS', $pieces);
    }

    /**
     * @dataProvider non__PATH_ROOTLESS__cases
     */
    public function test__PATH_ROOTLESS__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'PATH_ROOTLESS');
    }

    //
    // PATH_ABSOLUTE
    //
    public static function PATH_ABSOLUTE__strings()
    {
        $strings = [];
        $inheritedStrings = array_map(function (string $string) {
            return '/'.$string;
        }, static::PATH_ROOTLESS__strings());
        return array_merge($inheritedStrings, $strings);
    }

    public static function PATH_ABSOLUTE__cases()
    {
        $strings = static::PATH_ABSOLUTE__strings();
        return static::stringsToPregTuples($strings, 'path_absolute');
    }

    public static function non__PATH_ABSOLUTE__cases()
    {
        $strings = ["", "a", ":", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "?", "a/b"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider PATH_ABSOLUTE__cases
     */
    public function test__PATH_ABSOLUTE__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('path_absolute', $pieces);
        $this->assertRfcMatches($string, 'PATH_ABSOLUTE', $pieces);
    }

    /**
     * @dataProvider non__PATH_ABSOLUTE__cases
     */
    public function test__PATH_ABSOLUTE__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'PATH_ABSOLUTE');
    }

    //
    // PATH_ABEMPTY
    //
    public static function PATH_ABEMPTY__strings()
    {
        $strings = [];
        $inheritedStrings = array_merge(
            static::PATH_EMPTY__strings(),
            static::PATH_ABSOLUTE__strings()
        );
        return array_merge($inheritedStrings, $strings);
    }

    public static function PATH_ABEMPTY__cases()
    {
        $strings = static::PATH_ABEMPTY__strings();
        return static::stringsToPregTuples($strings, 'path_abempty');
    }

    public static function non__PATH_ABEMPTY__cases()
    {
        $strings = ["a", ":", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "?"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider PATH_ABEMPTY__cases
     */
    public function test__PATH_ABEMPTY__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('path_abempty', $pieces);
        $this->assertRfcMatches($string, 'PATH_ABEMPTY', $pieces);
    }

    /**
     * @dataProvider non__PATH_ABEMPTY__cases
     */
    public function test__PATH_ABEMPTY__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'PATH_ABEMPTY');
    }


    //
    // REG_NAME
    //
    public static function REG_NAME__strings()
    {
        return [
            "",
            "example.org",
            "!$&'()*+,;=aA2%1fx-._~",
        ];
    }

    public static function REG_NAME__cases()
    {
        $strings = static::REG_NAME__strings();
        return static::stringsToPregTuples($strings, 'reg_name');
    }

    public static function non__REG_NAME__cases()
    {
        $strings = [" ", "#", "%", "%1", "%1G", "%G", "%G2", "/", ":", "?", "@", "[", "]", "ł"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider REG_NAME__cases
     */
    public function test__REG_NAME__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'REG_NAME', $pieces);
    }

    /**
     * @dataProvider non__REG_NAME__cases
     */
    public function test__REG_NAME__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'REG_NAME');
    }

    //
    // DEC_OCTET
    //

    public static function DEC_OCTET__cases()
    {
        $strings = ["0", "7", "10", "45", "99", "100", "123", "199", "200", "234", "249", "250", "252", "255" ];
        return static::stringsToPregTuples($strings);
    }

    public static function non__DEC_OCTET__cases()
    {
        $strings = ["", " ", "#", "%", "%1", "%1G", "%G", "%G2", "/", ":", "?", "@", "[", "]", "ł",
                    "00", "05", "000", "010", "256",];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider DEC_OCTET__cases
     */
    public function test__DEC_OCTET__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'DEC_OCTET', $pieces);
    }

    /**
     * @dataProvider non__DEC_OCTET__cases
     */
    public function test__DEC_OCTET__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'DEC_OCTET');
    }

    //
    // IPV4ADDRESS
    //
    public static function IPV4ADDRESS__strings()
    {
        return [
            "0.0.0.0",
            "255.255.255.255",
            "192.168.0.2",
        ];
    }

    public static function IPV4ADDRESS__cases()
    {
        $strings = static::IPV4ADDRESS__strings();
        return static::stringsToPregTuples($strings, 'ipv4address');
    }

    public static function non__IPV4ADDRESS__cases()
    {
        $strings = [
            "", " ", "#",
            "1", "1.", "1.2", "1.2.", "1.2.3", "1.2.3.",
            "01.2.3.4", "1.02.3.4", "1.2.03.4", "1.2.3.04",
            "256.2.3.", "1.256.3.4", "1.2.256.4", "1.2.3.256",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider IPV4ADDRESS__cases
     */
    public function test__IPV4ADDRESS__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('ipv4address', $pieces);
        $this->assertRfcMatches($string, 'IPV4ADDRESS', $pieces);
    }

    /**
     * @dataProvider non__IPV4ADDRESS__cases
     */
    public function test__IPV4ADDRESS__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'IPV4ADDRESS');
    }

    //
    // H16
    //

    public function H16__strings()
    {
        return [
            "1", "9", "A", "F", "a", "f",
            "1a", "9d",
            "1ab", "93d",
            "1abc", "93df",
            "0000",
        ];
    }

    public function H16__cases()
    {
        return static::stringsToPregTuples(static::H16__strings());
    }

    public function non__H16__cases()
    {
        $strings = [
            "", " ", "g", "G", "12345", "abcde", "#", "%", "/", ":", "?", "@", "[", "]", "ł",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider H16__cases
     */
    public function test__H16__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'H16', $pieces);
    }

    /**
     * @dataProvider non__H16__cases
     */
    public function test__H16__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'H16');
    }

    //
    // LS32
    //
    public function LS32__strings()
    {
        return ["1:2", "12:34", "12a:2", "3:af23", "fed2:123a", "1.23.245.212"];
    }

    public function LS32__cases()
    {
        $strings = static::LS32__strings();
        return static::stringsToPregTuples($strings, 'ls32');
    }

    public function non__LS32__cases()
    {
        $strings = [
            "", " ", "g", "G", "123", "12345:123", "abcde:dff",
            "#", "%", "/", ":", "?", "@", "[", "]", "ł",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider LS32__cases
     */
    public function test__LS32__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('ls32', $pieces);
        $this->assertRfcMatches($string, 'LS32', $pieces);
    }

    /**
     * @dataProvider non__LS32__cases
     */
    public function test__LS32__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'LS32');
    }

    //
    // IPV6ADDRESS
    //

    public static function IPV6ADDRESS__cases()
    {
        $cases = [
            [
                "::",                           // any address compression
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "::1",                          // localhost IPv6 address
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "1::",                          // trailing compression
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122
            //   0123456789012345678901
                "::ffff:192.168.173.22",        // IPv4 space
                [
                    'ls32'          => ['192.168.173.22', 7],
                    'ipv6v4address' => ['192.168.173.22', 7],
                ]
            ],
            // some real-life examples
            [
            //   000000000011111111112222
            //   012345678901234567890123
                "2605:2700:0:3::4713:93e3",
                [
                    'ls32'          => ['4713:93e3', 15],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122
            //   0123456789012345678901
                "2a02:a311:161:9d80::1",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
        ];

        for ($i = 0; $i < count($cases); $i++) {
            $cases[$i] = static::transformPregTuple($cases[$i], [
                'merge' => ['ipv6address' => [$cases[$i][0], 0]]
            ]);
        }

        return $cases;
    }

    public static function extra__IPV6ADDRESS__cases()
    {
        $cases = [
            // 1'st row in rule
            [
            //   0000000000111111111122222222223
            //   0123456789012345678901234567890
                "99:aa:bbb:cccc:dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 25],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "99:aa:bbb:cccc:dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 25],
                    'ipv6v4address' => ['192.168.173.22', 25],
                ]
            ],

            // 2'nd row in rule
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::aa:bbb:cccc:dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 24],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::aa:bbb:cccc:dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 24],
                    'ipv6v4address' => ['192.168.173.22', 24],
                ]
            ],

            // 3'rd row in rule
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::bbb:cccc:dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 21],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::bbb:cccc:dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 21],
                    'ipv6v4address' => ['192.168.173.22', 21],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::bbb:cccc:dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 23],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::bbb:cccc:dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 23],
                    'ipv6v4address' => ['192.168.173.22', 23],
                ]
            ],

            // 4'th row in rule
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::cccc:dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 17],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::cccc:dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 17],
                    'ipv6v4address' => ['192.168.173.22', 17],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::cccc:dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 19],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::cccc:dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 19],
                    'ipv6v4address' => ['192.168.173.22', 19],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::cccc:dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 22],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::cccc:dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 22],
                    'ipv6v4address' => ['192.168.173.22', 22],
                ]
            ],

            // 5'th row in rule
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 12],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 12],
                    'ipv6v4address' => ['192.168.173.22', 12],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 14],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 14],
                    'ipv6v4address' => ['192.168.173.22', 14],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 17],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 17],
                    'ipv6v4address' => ['192.168.173.22', 17],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33::dddd:eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 20],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33::dddd:eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 20],
                    'ipv6v4address' => ['192.168.173.22', 20],
                ]
            ],

            // 6'th row in rule
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 7],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 7],
                    'ipv6v4address' => ['192.168.173.22', 7],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 9],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 9],
                    'ipv6v4address' => ['192.168.173.22', 9],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 12],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 12],
                    'ipv6v4address' => ['192.168.173.22', 12],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33::eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 15],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33::eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 15],
                    'ipv6v4address' => ['192.168.173.22', 15],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33:44::eeee:ff:32",
                [
                    'ls32'          => ['ff:32', 18],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33:44::eeee:192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 18],
                    'ipv6v4address' => ['192.168.173.22', 18],
                ]
            ],

            // 7'th row in rule
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::ff:32",
                [
                    'ls32'          => ['ff:32', 2],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "::192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 2],
                    'ipv6v4address' => ['192.168.173.22', 2],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::ff:32",
                [
                    'ls32'          => ['ff:32', 4],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11::192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 4],
                    'ipv6v4address' => ['192.168.173.22', 4],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::ff:32",
                [
                    'ls32'          => ['ff:32', 7],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22::192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 7],
                    'ipv6v4address' => ['192.168.173.22', 7],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33::ff:32",
                [
                    'ls32'          => ['ff:32', 10],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33::192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 10],
                    'ipv6v4address' => ['192.168.173.22', 10],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33:44::ff:32",
                [
                    'ls32'          => ['ff:32', 13],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33:44::192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 13],
                    'ipv6v4address' => ['192.168.173.22', 13],
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33:44:55::ff:32",
                [
                    'ls32'          => ['ff:32', 16],
                    'ipv6v4address' => false,
                ]
            ],
            [
            //   0000000000111111111122222222223333333333
            //   0123456789012345678901234567890123456789
                "11:22:33:44:55::192.168.173.22",
                [
                    'ls32'          => ['192.168.173.22', 16],
                    'ipv6v4address' => ['192.168.173.22', 16],
                ]
            ],

            // 8'th row in rule
            [
                "::ff",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11::ff",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22::ff",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33::ff",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33:44::ff",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33:44:55::ff",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33:44:55:66::ff",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],

            // 9'th row in rule
            [
                "::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33:44::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33:44:55::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33:44:55:66::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
            [
                "11:22:33:44:55:66:77::",
                [
                    'ls32'          => false,
                    'ipv6v4address' => false,
                ]
            ],
        ];

        for ($i = 0; $i < count($cases); $i++) {
            $cases[$i] = static::transformPregTuple($cases[$i], [
                'merge' => ['ipv6address' => [$cases[$i][0], 0]]
            ]);
        }

        return $cases;
    }

    public static function non__IPV6ADDRESS__cases()
    {
        $strings = [
            "", " ", "g", "G", "123", "12345:123", "abcde:dff",
            "#", "%", "/", ":", "?", "@", "[", "]", "ł",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider IPV6ADDRESS__cases
     * @dataProvider extra__IPV6ADDRESS__cases
     */
    public function test__IPV6ADDRESS__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('ipv6address', $pieces);
        $this->assertRfcMatches($string, 'IPV6ADDRESS', $pieces);
    }

    /**
     * @dataProvider non__IPV6ADDRESS__cases
     */
    public function test__IPV6ADDRESS__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'IPV6ADDRESS');
    }

    //
    // IPVFUTURE
    //
    public static function IPVFUTURE__strings()
    {
        return [
            "v12ea.:!$&'()*+,;=-._~aB32",
        ];
    }

    public static function IPVFUTURE__cases()
    {
        $strings = static::IPVFUTURE__strings();
        return static::stringsToPregTuples($strings, 'ipvfuture');
    }

    public static function non__IPVFUTURE__cases()
    {
        $strings = [
            "", " ", "a", "B", "1", "vGEE.aa", "v.sdf", "#", "%", "/", ":", "?", "@", "[", "]", "ł",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider IPVFUTURE__cases
     */
    public function test__IPVFUTURE__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('ipvfuture', $pieces);
        $this->assertRfcMatches($string, 'IPVFUTURE', $pieces);
    }

    /**
     * @dataProvider non__IPVFUTURE__cases
     */
    public function test__IPVFUTURE__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'IPVFUTURE');
    }

    //
    // IP_LITERAL
    //

    public static function IP_LITERAL__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::IPV6ADDRESS__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => '[',
                'suffix' => ']',
                'merge' => [
                    'ip_literal' => ['['.$case[0].']', 0],
                    'ipvfuture' => false,
                ]
            ]);
        }
        foreach (static::IPVFUTURE__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'prefix' => '[',
                'suffix' => ']',
                'merge' => [
                    'ip_literal' => ['['.$case[0].']', 0],
                    'ipv6address' => false,
                    'ls32' => false,
                    'ipv6v4address' => false,
                    'ipvfuture' => [$case[0], 1],
                ]
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__IP_LITERAL__cases()
    {
        $strings = [
            "", " ", "g", "G", "123", "12345:123", "abcde:dff",
            "#", "%", "/", ":", "?", "@", "[", "]", "ł",
            "::",
            "::1",
            "1::",
            "::ffff:192.168.173.22",
            "2605:2700:0:3::4713:93e3",
            "2a02:a311:161:9d80::1",
            "fe80::ce71:d980:66d:c516",
            "2a02:a311:161:9d80:7aed:ddca:5162:f673",
            "v1.:",
            "v2f.:",
            "v12ea.:!$&'()*+,;=-._~aB32",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider IP_LITERAL__cases
     */
    public function test__IP_LITERAL__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('ip_literal', $pieces);
        $this->assertRfcMatches($string, 'IP_LITERAL', $pieces);
    }

    /**
     * @dataProvider non__IP_LITERAL__cases
     */
    public function test__IP_LITERAL__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'IP_LITERAL');
    }

    //
    // PORT
    //

    public static function PORT__cases()
    {
        $strings = ["", "123"];
        return static::stringsToPregTuples($strings, 'port');
    }

    public static function non__PORT__cases()
    {
        $strings = ["a", "A", "@"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider PORT__cases
     */
    public function test__PORT__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('port', $pieces);
        $this->assertRfcMatches($string, 'PORT', $pieces);
    }

    /**
     * @dataProvider non__PORT__cases
     */
    public function test__PORT__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'PORT');
    }

    //
    // HOST
    //

    public static function HOST__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::IP_LITERAL__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'merge' => [
                    'host' => [$case[0], 0],
                    'ipv4address' => false,
                    'reg_name' => false
                ]
            ]);
        }
        foreach (static::IPV4ADDRESS__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'merge' => [
                    'host' => [$case[0], 0],
                    'ip_literal' => false,
                    'ipv6address' => false,
                    'ls32' => false,
                    'ipv6v4address' => false,
                    'ipvfuture' => false,
                    'ipv4address' => [$case[0], 0],
                    'reg_name' => false,
                ]
            ]);
        }
        foreach (static::REG_NAME__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'merge' => [
                    'host' => [$case[0], 0],
                    'ip_literal' => false,
                    'ipv6address' => false,
                    'ls32' => false,
                    'ipv6v4address' => false,
                    'ipvfuture' => false,
                    'ipv4address' => false,
                    'reg_name' => [$case[0], 0],
                ]
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__HOST__cases()
    {
        $strings = [
            " ", "12345:123", "abcde:dff",
            "#", "%", "/", ":", "?", "@", "[", "]", "ł",
            "::",
            "::1",
            "1::",
            "::ffff:192.168.173.22",
            "2605:2700:0:3::4713:93e3",
            "2a02:a311:161:9d80::1",
            "fe80::ce71:d980:66d:c516",
            "2a02:a311:161:9d80:7aed:ddca:5162:f673",
            "v1.:",
            "v2f.:",
            "v12ea.:!$&'()*+,;=-._~aB32",
            "[asdfgh%]",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider HOST__cases
     */
    public function test__HOST__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('host', $pieces);
        $this->assertRfcMatches($string, 'HOST', $pieces);
    }

    /**
     * @dataProvider non__HOST__cases
     */
    public function test__HOST__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'HOST');
    }

    //
    // USERINFO
    //

    public static function USERINFO__cases()
    {
        $strings = ["", "!$&'()*+,;=-._~Ab1%1fx:"];
        return static::stringsToPregTuples($strings, 'userinfo');
    }

    public static function non__USERINFO__cases()
    {
        $strings = [
            "%", "%1", "%G", "%1G", "%G2", "#", "ł",
            "/", "?", "/foo/../BaR?aa=12&bb=4adf,hi/dood",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider USERINFO__cases
     */
    public function test__USERINFO__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('userinfo', $pieces);
        $this->assertRfcMatches($string, 'USERINFO', $pieces);
    }

    /**
     * @dataProvider non__USERINFO__cases
     */
    public function test__USERINFO__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'USERINFO');
    }

    //
    // AUTHORITY
    //

    public static function AUTHORITY__cases()
    {
        $cases = [];

        $inheritedCases = [];
        foreach (static::USERINFO__cases() as $user) {
            foreach (static::HOST__cases() as $host) {
                $userHost = static::joinPregTuples([$user, $host], [
                    'glue' => '@',
                    'merge' => [
                        'authority' => [$user[0].'@'.$host[0], 0],
                        'port' => false
                    ]
                ]);
                $inheritedCases[] = $userHost;
                foreach (static::PORT__cases() as $port) {
                    $inheritedCases[] = static::joinPregTuples([$userHost, $port], [
                        'glue' => ':',
                        'merge' => [
                            'authority' => [$userHost[0].':'.$port[0], 0],
                        ]
                    ]);
                }
            }
        }

        foreach (static::HOST__cases() as $host) {
            $inheritedCases[] = static::transformPregTuple($host, [
                'merge' => [
                    'authority' => [$host[0], 0],
                    'userinfo' => false,
                    'port' => false
                ]
            ]);
            foreach (static::PORT__cases() as $port) {
                $inheritedCases[] = static::joinPregTuples([$host, $port], [
                    'glue' => ':',
                    'merge' => [
                        'authority' => [$host[0].':'.$port[0], 0],
                        'userinfo' => false,
                    ],
                ]);
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__AUTHORITY__cases()
    {
        $strings = [
            "%", "%1", "%G", "%1G", "%G2", "#", "ł",
            "/", "?", "/foo/../BaR?aa=12&bb=4adf,hi/dood",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider AUTHORITY__cases
     */
    public function test__AUTHORITY__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('authority', $pieces);
        $this->assertArrayHasKey('userinfo', $pieces);
        $this->assertArrayHasKey('host', $pieces);
        $this->assertArrayHasKey('port', $pieces);
        $this->assertRfcMatches($string, 'AUTHORITY', $pieces);
    }

    /**
     * @dataProvider non__AUTHORITY__cases
     */
    public function test__AUTHORITY__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'AUTHORITY');
    }

    //
    // SCHEME
    //

    public static function SCHEME__cases()
    {
        $strings = ["a.23+x-x"];
        return static::stringsToPregTuples($strings, 'scheme');
    }

    public static function non__SCHEME__cases()
    {
        $strings = ["", "1s", "@", "a~"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider SCHEME__cases
     */
    public function test__SCHEME__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('scheme', $pieces);
        $this->assertRfcMatches($string, 'SCHEME', $pieces);
    }

    /**
     * @dataProvider non__SCHEME__cases
     */
    public function test__SCHEME__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'SCHEME');
    }

    //
    // RELATIVE_PART
    //

    public static function RELATIVE_PART__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::AUTHORITY__cases() as $authority) {
            foreach (static::PATH_ABEMPTY__cases() as $path) {
                $inheritedCases[] = static::joinPregTuples([$authority, $path], [
                    'prefix' => '//',
                    'merge' => [
                        'relative_part' => ['//'.$authority[0].$path[0], 0]
                    ],
                ]);
            }
        }
        foreach (static::PATH_ABSOLUTE__cases() as $path) {
            $inheritedCases[] = static::transformPregTuple($path, [
                'merge' => ['relative_part' => [$path[0], 0]]
            ]);
        }
        foreach (static::PATH_NOSCHEME__cases() as $path) {
            $inheritedCases[] = static::transformPregTuple($path, [
                'merge' => ['relative_part' => [$path[0], 0]]
            ]);
        }
        foreach (static::PATH_EMPTY__cases() as $path) {
            $inheritedCases[] = static::transformPregTuple($path, [
                'merge' => ['relative_part' => [$path[0], 0]]
            ]);
        }

        return array_merge($inheritedCases, $cases);
    }

    public static function non__RELATIVE_PART__cases()
    {
        $strings = ["#", "%", "%1", "%1G", "%G", "%G2", ":", ":/", "?", "ł"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider RELATIVE_PART__cases
     */
    public function test__RELATIVE_PART__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('relative_part', $pieces);
        $this->assertRfcMatches($string, 'RELATIVE_PART', $pieces);
    }

    /**
     * @dataProvider non__RELATIVE_PART__cases
     */
    public function test__RELATIVE_PART__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'RELATIVE_PART');
    }

    //
    // HIER_PART
    //

    public static function HIER_PART__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::AUTHORITY__cases() as $authority) {
            foreach (static::PATH_ABEMPTY__cases() as $path) {
                $inheritedCases[] = static::joinPregTuples([$authority, $path], [
                    'prefix' => '//',
                    'merge' => [
                        'hier_part' => ['//'.$authority[0].$path[0], 0],
                        'path_absolute' => false,
                        'path_rootless' => false,
                        'path_empty' => false,
                    ],
                ]);
            }
        }
        foreach (static::PATH_ABSOLUTE__cases() as $path) {
            $inheritedCases[] = static::transformPregTuple($path, [
                'merge' => [
                    'hier_part' => [$path[0], 0],
                    'authority' => false,
                    'path_abempty' => false,
                    'path_rootless' => false,
                    'path_empty' => false,
                ],
            ]);
        }
        foreach (static::PATH_ROOTLESS__cases() as $path) {
            $inheritedCases[] = static::transformPregTuple($path, [
                'merge' => [
                    'hier_part' => [$path[0], 0],
                    'authority' => false,
                    'path_abempty' => false,
                    'path_absolute' => false,
                    'path_empty' => false,
                ],
            ]);
        }
        foreach (static::PATH_EMPTY__cases() as $path) {
            $inheritedCases[] = static::transformPregTuple($path, [
                'merge' => [
                    'hier_part' => [$path[0], 0],
                    'authority' => false,
                    'path_abempty' => false,
                    'path_absolute' => false,
                    'path_rootless' => false,
                ],
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__HIER_PART__cases()
    {
        $strings = ["#", "%", "%1", "%1G", "%G", "%G2", "?", "ł"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider HIER_PART__cases
     */
    public function test__HIER_PART__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('hier_part', $pieces);
        $this->assertArrayHasKey('authority', $pieces);
        $this->assertArrayHasKey('path_abempty', $pieces);
        $this->assertArrayHasKey('path_absolute', $pieces);
        $this->assertArrayHasKey('path_rootless', $pieces);
        $this->assertArrayHasKey('path_empty', $pieces);
        $this->assertRfcMatches($string, 'HIER_PART', $pieces);
    }

    /**
     * @dataProvider non__HIER_PART__cases
     */
    public function test__HIER_PART__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'HIER_PART');
    }

    //
    // FRAGMENT
    //

    public static function FRAGMENT__cases()
    {
        $strings = [
            "", 'aZ2-._~!$&\'()*+,;=/?:@%20'
        ];
        return static::stringsToPregTuples($strings, 'fragment');
    }

    public static function non__FRAGMENT__cases()
    {
        $strings = ["%", "%1", "%G", "%1G", "%G2", "#", "ł"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider FRAGMENT__cases
     */
    public function test__FRAGMENT__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('fragment', $pieces);
        $this->assertRfcMatches($string, 'FRAGMENT', $pieces);
    }

    /**
     * @dataProvider non__FRAGMENT__cases
     */
    public function test__FRAGMENT__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'FRAGMENT');
    }

    //
    // QUERY
    //

    public static function QUERY__cases()
    {
        $strings = [
            "", 'aZ2-._~!$&\'()*+,;=/?:@%20'
        ];
        return static::stringsToPregTuples($strings, 'query');
    }

    public static function non__QUERY__cases()
    {
        $strings = ["%", "%1", "%G", "%1G", "%G2", "#", "ł"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider QUERY__cases
     */
    public function test__QUERY__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('query', $pieces);
        $this->assertRfcMatches($string, 'QUERY', $pieces);
    }

    /**
     * @dataProvider non__QUERY__cases
     */
    public function test__QUERY__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'QUERY');
    }

    //
    // RELATIVE_REF
    //

    public static function RELATIVE_REF__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::RELATIVE_PART__cases() as $relPart) {
            $relPartRef = static::transformPregTuple($relPart, [
                'merge' => [
                    'relative_ref' => [$relPart[0], 0],
                    'query' => false,
                    'fragment' => false,
                ]
            ]);
            $inheritedCases[] = $relPartRef;
            foreach (static::QUERY__cases() as $query) {
                $relPartQuery = static::joinPregTuples([$relPartRef, $query], [
                    'glue' => '?',
                    'merge' => [
                        'relative_ref' => [$relPartRef[0].'?'.$query[0], 0],
                        'fragment' => false,
                    ]
                ]);
                $inheritedCases[] = $relPartQuery;
                foreach (static::FRAGMENT__cases() as $fragment) {
                    $relPartQueryFrag = static::joinPregTuples([$relPartQuery, $fragment], [
                        'glue' => '#',
                        'merge' => [
                            'relative_ref' => [$relPartQuery[0].'#'.$fragment[0], 0]
                        ]
                    ]);
                    $inheritedCases[] = $relPartQueryFrag;
                }
            }
            foreach (static::FRAGMENT__cases() as $fragment) {
                $relPartFrag = static::joinPregTuples([$relPartRef, $fragment], [
                    'glue' => '#',
                    'merge' => [
                        'relative_ref' => [$relPartRef[0].'#'.$fragment[0], 0],
                        'query' => false,
                    ]
                ]);
                $inheritedCases[] = $relPartFrag;
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__RELATIVE_REF__cases()
    {
        $strings = ["%", "%1", "%1G", "%G", "%G2", ":", ":/", "ł"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider RELATIVE_REF__cases
     */
    public function test__RELATIVE_REF__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('relative_ref', $pieces);
        $this->assertArrayHasKey('relative_part', $pieces);
        $this->assertArrayHasKey('query', $pieces);
        $this->assertArrayHasKey('fragment', $pieces);
        $this->assertRfcMatches($string, 'RELATIVE_REF', $pieces);
    }

    /**
     * @dataProvider non__RELATIVE_REF__cases
     */
    public function test__RELATIVE_REF__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'RELATIVE_REF');
    }

    //
    // ABSOLUTE_URI
    //

    public static function ABSOLUTE_URI__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::SCHEME__cases() as $scheme) {
            foreach (static::HIER_PART__cases() as $hierpart) {
                $schemeHierpart = static::joinPregTuples([$scheme, $hierpart], [
                    'glue' => ':',
                    'merge' => array_merge($scheme[1] ?? [], [
                        'absolute_uri' => [$scheme[0].':'.$hierpart[0], 0],
                        'query' => false
                    ])
                ]);
                $inheritedCases[] = $schemeHierpart;
                foreach (static::QUERY__cases() as $query) {
                    $inheritedCases[] = static::joinPregTuples([$schemeHierpart, $query], [
                        'glue' => '?',
                        'merge' => [
                            'absolute_uri' => [$schemeHierpart[0].'?'.$query[0], 0],
                        ]
                    ]);
                }
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__ABSOLUTE_URI__cases()
    {
        $strings = [
            "",
            ":",
            ":foo",
            "scheme",
            "http://example.com/foo#arg1=v1&arg2=v2"
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider ABSOLUTE_URI__cases
     */
    public function test__ABSOLUTE_URI__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('absolute_uri', $pieces);
        $this->assertArrayHasKey('scheme', $pieces);
        $this->assertArrayHasKey('hier_part', $pieces);
        $this->assertArrayHasKey('query', $pieces);
        $this->assertRfcMatches($string, 'ABSOLUTE_URI', $pieces);
    }

    /**
     * @dataProvider non__ABSOLUTE_URI__cases
     */
    public function test__ABSOLUTE_URI__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'ABSOLUTE_URI');
    }

    //
    // URI
    //

    public static function URI__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::ABSOLUTE_URI__cases() as $absUri) {
            $inheritedCases[] = static::transformPregTuple($absUri, [
                'merge' => [
                    'uri' => [$absUri[0], 0],
                    'absolute_uri' => false,
                    'fragment'=> false,
                ],
            ]);
            foreach (static::FRAGMENT__cases() as $fragment) {
                $inheritedCases[] = static::joinPregTuples([$absUri, $fragment], [
                    'glue' => '#',
                    'merge' => [
                        'uri' => [$absUri[0].'#'.$fragment[0], 0],
                        'absolute_uri' => false,
                    ],
                ]);
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__URI__cases()
    {
        $strings = [
            "",
            ":",
            ":foo",
            "scheme",
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider URI__cases
     */
    public function test__URI__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('uri', $pieces);
        $this->assertArrayHasKey('scheme', $pieces);
        $this->assertArrayHasKey('hier_part', $pieces);
        $this->assertArrayHasKey('query', $pieces);
        $this->assertArrayHasKey('fragment', $pieces);
        $this->assertRfcMatches($string, 'URI', $pieces);
    }

    /**
     * @dataProvider non__URI__cases
     */
    public function test__URI__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'URI');
    }

    //
    // URI_REFERENCE
    //

    public static function URI_REFERENCE__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (static::URI__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'merge' => [
                    'uri_reference' => [$case[0], 0],
                    'uri' => [$case[0], 0],
                    'relative_ref' => false,
                ]
            ]);
        }
        foreach (static::RELATIVE_REF__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'merge' => [
                    'uri_reference' => [$case[0], 0],
                    'uri' => false,
                    'relative_ref' => [$case[0], 0],
                ]
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__URI_REFERENCE__cases()
    {
        $strings = [
            ':',
            ':foo',
        ];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider URI_REFERENCE__cases
     */
    public function test__URI_REFERENCE__matches(string $string, array $pieces = [])
    {
        $this->assertArrayHasKey('uri_reference', $pieces);
        $this->assertArrayHasKey('uri', $pieces);
        $this->assertArrayHasKey('relative_ref', $pieces);
        $this->assertRfcMatches($string, 'URI_REFERENCE', $pieces);
    }

    /**
     * @dataProvider non__URI_REFERENCE__cases
     */
    public function test__URI_REFERENCE__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'URI_REFERENCE');
    }
}

// vim: syntax=php sw=4 ts=4 et:
