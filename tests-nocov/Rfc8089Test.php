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

use Korowai\Lib\Rfc\Rfc8089;
use Korowai\Lib\Rfc\Rfc3986;
use Korowai\Testing\Rfclib\TestCase;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Rfc8089Test extends TestCase
{
    public static function getRfcClass() : string
    {
        return Rfc8089::class;
    }

    //
    // FILE_AUTH
    //

    public function FILE_AUTH__cases()
    {
        $cases = [
            [
                'localhost',
                [
                    'host' => false,
                    'file_auth' => ['localhost', 0],
                ]
            ]
        ];
        $inheritedCases = [];
        foreach (Rfc3986Test::HOST__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'merge' => [
                    'file_auth' => [$case[0], 0]
                ]
            ]);
        }

        return array_merge($inheritedCases, $cases);
    }

    public function non__FILE_AUTH__cases()
    {
        $strings = [];
        $inheritedCases = Rfc3986Test::non__HOST__cases();
        return array_merge($inheritedCases, static::stringsToPregTuples($strings));
    }

    /**
     * @dataProvider FILE_AUTH__cases
     */
    public function test__FILE_AUTH__matches(string $string, array $pieces)
    {
        $this->assertArrayHasKey('file_auth', $pieces);
        $this->assertArrayHasKey('host', $pieces);
        $this->assertRfcMatches($string, 'FILE_AUTH', $pieces);
    }

    /**
     * @dataProvider non__FILE_AUTH__cases
     */
    public function test__FILE_AUTH__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'FILE_AUTH');
    }

    //
    // LOCAL_PATH
    //

    public function LOCAL_PATH__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (Rfc3986Test::PATH_ABSOLUTE__cases() as $case) {
            $inheritedCases[] = static::transformPregTuple($case, [
                'merge' => [
                    'local_path' => [$case[0], 0],
                ],
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public function non__LOCAL_PATH__cases()
    {
        $strings = [
        ];
        return array_merge(
            static::stringsToPregTuples($strings),
            Rfc3986Test::non__PATH_ABSOLUTE__cases()
        );
    }

    /**
     * @dataProvider LOCAL_PATH__cases
     */
    public function test__LOCAL_PATH__matches(string $string, array $pieces)
    {
        $this->assertArrayHasKey('local_path', $pieces);
        $this->assertArrayHasKey('path_absolute', $pieces);
        $this->assertRfcMatches($string, 'LOCAL_PATH', $pieces);
    }

    /**
     * @dataProvider non__LOCAL_PATH__cases
     */
    public function test__LOCAL_PATH__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'LOCAL_PATH');
    }

    //
    // AUTH_PATH
    //

    public function AUTH_PATH__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (Rfc3986Test::PATH_ABSOLUTE__cases() as $path) {
            $inheritedCases[] = static::transformPregTuple($path, [
                'merge' => [
                    'auth_path' => [$path[0], 0],
                    'file_auth' => ['', 0],
                    'path_absolute' => [$path[0], 0],
                ]
            ]);
            foreach (static::FILE_AUTH__cases() as $fileAuth) {
                $inheritedCases[] = static::joinPregTuples([$fileAuth, $path], [
                    'merge' => [
                        'auth_path' => [$fileAuth[0].$path[0], 0],
                    ]
                ]);
            }
        }
        return array_merge($cases, $inheritedCases);
    }

    public function non__AUTH_PATH__cases()
    {
        $strings = ["", "a", ":", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "?", "1.2.3.4"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider AUTH_PATH__cases
     */
    public function test__AUTH_PATH__matches(string $string, array $pieces)
    {
        $this->assertArrayHasKey('auth_path', $pieces);
        $this->assertArrayHasKey('file_auth', $pieces);
        $this->assertArrayHasKey('path_absolute', $pieces);
        $this->assertRfcMatches($string, 'AUTH_PATH', $pieces);
    }

    /**
     * @dataProvider non__AUTH_PATH__cases
     */
    public function test__AUTH_PATH__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'AUTH_PATH');
    }

    //
    // FILE_HIER_PART
    //

    public function FILE_HIER_PART__cases()
    {
        $cases = [];
        $inheritedCases = [];
        foreach (self::AUTH_PATH__cases() as $authPath) {
            $inheritedCases[] = static::transformPregTuple($authPath, [
                'prefix' => '//',
                'merge' => [
                    'file_hier_part' => ['//'.$authPath[0], 0],
                    'local_path' => false,
                ]
            ]);
        }
        foreach (self::LOCAL_PATH__cases() as $localPath) {
            $inheritedCases[] = static::transformPregTuple($localPath, [
                'merge' => [
                    'file_hier_part' => [$localPath[0], 0],
                    'auth_path' => false
                ],
            ]);
        }
        return array_merge($inheritedCases, $cases);
    }

    public function non__FILE_HIER_PART__cases()
    {
        $strings = ["", "a", ":", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "?", "1.2.3.4"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider FILE_HIER_PART__cases
     */
    public function test__FILE_HIER_PART__matches(string $string, array $pieces)
    {
        $this->assertArrayHasKey('file_hier_part', $pieces);
        $this->assertArrayHasKey('auth_path', $pieces);
        $this->assertArrayHasKey('local_path', $pieces);
        $this->assertRfcMatches($string, 'FILE_HIER_PART', $pieces);
    }

    /**
     * @dataProvider non__FILE_HIER_PART__cases
     */
    public function test__FILE_HIER_PART__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'FILE_HIER_PART');
    }

    //
    // FILE_SCHEME
    //

    public function test__FILE_SCHEME()
    {
        $this->assertSame('(?<file_scheme>file)', Rfc8089::FILE_SCHEME);
    }

    //
    // FILE_URI
    //

    public function FILE_URI__cases()
    {
        $cases = [
            [
            //   00000000001111
            //   01234567890123
                'file:/',
                [
                    'file_uri'          => ['file:/', 0],
                    'file_scheme'       => ['file', 0],
                    'file_hier_part'    => ['/', 5],
                    'file_auth'         => false,
                    'host'              => false,
                    'local_path'        => ['/', 5],
                    'path_absolute'     => ['/', 5],
                ]
            ],
            [
            //   00000000001111
            //   01234567890123
                'file:/foo/bar',
                [
                    'file_uri'          => ['file:/foo/bar', 0],
                    'file_scheme'       => ['file', 0],
                    'file_hier_part'    => ['/foo/bar', 5],
                    'file_auth'         => false,
                    'host'              => false,
                    'local_path'        => ['/foo/bar', 5],
                    'path_absolute'     => ['/foo/bar', 5],
                ]
            ],
        ];

        $fileScheme = ['file', ['file_scheme' => ['file', 0]]];

        $inheritedCases = [];
        foreach (self::FILE_HIER_PART__cases() as $hierPart) {
            $inheritedCases[] = static::joinPregTuples([$fileScheme, $hierPart], [
                'glue' => ':',
                'merge' => [
                    'file_uri' => [$fileScheme[0].':'.$hierPart[0], 0],
                ],
            ]);
        }
        return array_merge($cases, $inheritedCases);
    }

    public function non__FILE_URI__cases()
    {
        $strings = ["", "a", ":", "%", "%1", "%G", "%1G", "%G2", "#", "ł", "?", "1.2.3.4", "file:"];
        return static::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider FILE_URI__cases
     */
    public function test__FILE_URI__matches(string $string, array $pieces)
    {
        $this->assertArrayHasKey('file_uri', $pieces);
        $this->assertArrayHasKey('file_scheme', $pieces);
        $this->assertArrayHasKey('file_hier_part', $pieces);
        $this->assertRfcMatches($string, 'FILE_URI', $pieces);
    }

    /**
     * @dataProvider non__FILE_URI__cases
     */
    public function test__FILE_URI__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'FILE_URI');
    }
}

// vim: syntax=php sw=4 ts=4 et:
