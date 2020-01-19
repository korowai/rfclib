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

use Korowai\Lib\Rfc\Rfc2253;
use Korowai\Lib\Rfc\AbstractRuleSet;
use Korowai\Testing\Rfclib\TestCase;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Rfc2253Test extends TestCase
{
    public static function getRfcClass() : string
    {
        return Rfc2253::class;
    }

    public function test__characterClasses()
    {
        // character lists for character classes
        $this->assertSame('A-Za-z', Rfc2253::ALPHACHARS);
        $this->assertSame('0-9', Rfc2253::DIGITCHARS);
        $this->assertSame('0-9A-Fa-f', Rfc2253::HEXDIGCHARS);
        $this->assertSame(',=+<>#;', Rfc2253::SPECIALCHARS);
        $this->assertSame('0-9A-Za-z-', Rfc2253::KEYCHARCHARS);

        // character classes
        $this->assertSame('[A-Za-z]', Rfc2253::ALPHA);
        $this->assertSame('[0-9]', Rfc2253::DIGIT);
        $this->assertSame('[0-9A-Fa-f]', Rfc2253::HEXCHAR);
        $this->assertSame('[,=+<>#;]', Rfc2253::SPECIAL);
        $this->assertSame('[0-9A-Za-z-]', Rfc2253::KEYCHAR);
        $this->assertSame('[^,=+<>#;\\\\"]', Rfc2253::STRINGCHAR);
        $this->assertSame('[^\\\\"]', Rfc2253::QUOTECHAR);
    }

    public function test__simpleProductions()
    {
        $this->assertSame('(?:[0-9A-Fa-f][0-9A-Fa-f])', Rfc2253::HEXPAIR);
        $this->assertSame('(?:(?:[0-9A-Fa-f][0-9A-Fa-f])+)', Rfc2253::HEXSTRING);
        $this->assertSame('(?:\\\\(?:[,=+<>#;\\\\"]|(?:[0-9A-Fa-f][0-9A-Fa-f])))', Rfc2253::PAIR);
        $this->assertSame('(?:[0-9]+(?:\.[0-9]+)*)', Rfc2253::OID);
    }

    //
    // OID
    //

    public static function OID__cases()
    {
        $strings = ['1', '1.23', '1.23.456'];
        return self::stringsToPregTuples($strings);
    }

    public static function non__OID__cases()
    {
        $strings = ['', '~', 'O', '1.', '.1', '1.23.', 'a', 'ab', 'ab.cd'];
        return self::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider OID__cases
     */
    public function test__OID__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'OID', $pieces);
    }

    /**
     * @dataProvider non__OID__cases
     */
    public function test__OID__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'OID');
    }

    //
    // STRING
    //

    public static function STRING__cases()
    {
        $strings = [
            '',
            '\\20',
            "aAłL123!@$%^&*()~- \t",            // stringchars
            "#203039",                          // HEXSTRING
            '""',                               // empty quoted string
            '"aA\\"\\\\\\20lŁ21!@#$%^&*()"',    // non-empty quoted string
        ];
        return static::stringsToPregTuples($strings);
    }

    public static function non__STRING__cases()
    {
        $strings = [
            '\\',       // incomplete pair
            '\x',       // incomplete pair
            '#x',       // incomplete HEXSTRING
            '#299',     // incomplete string_hex
            '"foo',     // unterminated quoted string
            '"',        // unterminated quoted string
        ];
        return self::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider STRING__cases
     */
    public function test__STRING__matches(string $string, array $pieces = [])
    {
        $this->assertRfcMatches($string, 'STRING', $pieces);
    }

    /**
     * @dataProvider non__STRING__cases
     */
    public function test__STRING__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'STRING');
    }

    //
    // ATTRIBUTE_VALUE
    //

    public static function ATTRIBUTE_VALUE__cases()
    {
        return self::STRING__cases();
    }

    public function test__ATTRIBUTE_VALUE()
    {
        $this->assertSame(Rfc2253::STRING, Rfc2253::ATTRIBUTE_VALUE);
    }

    //
    // ATTRIBUTE_TYPE
    //

    public static function ATTRIBUTE_TYPE__cases()
    {
        $strings = [
            'O',
            'OU-12-',
            '1',
            '1.2.3',
        ];
        return self::stringsToPregTuples($strings);
    }

    public static function non__ATTRIBUTE_TYPE__cases()
    {
        $strings = [
            '',
            '~',
            '-O',
            'O~',
            '1.',
        ];
        return self::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider ATTRIBUTE_TYPE__cases
     */
    public function test__ATTRIBUTE_TYPE__matches(string $string, array $pieces =[])
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
    // ATTRIBUTE_TYPE_AND_VALUE
    //

    public static function ATTRIBUTE_TYPE_AND_VALUE__cases()
    {
        $cases = [
        ];
        $inheritedCases = [];
        foreach (self::ATTRIBUTE_TYPE__cases() as $type) {
            foreach (self::ATTRIBUTE_VALUE__cases() as $value) {
                $case = [
                    $type[0].'='.$value[0],
                    array_merge($type[1] ?? [], $value[1] ?? [])
                ];
                $inheritedCases[] = $case;
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__ATTRIBUTE_TYPE_AND_VALUE__cases()
    {
        $strings = [
            '',
            '~',
            '-O',
            'O~',
            '1.',
            '=',
            '=asdf',
            'O = 1',
        ];
        return self::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider ATTRIBUTE_TYPE_AND_VALUE__cases
     */
    public function test__ATTRIBUTE_TYPE_AND_VALUE__matches(string $string, array $pieces =[])
    {
        $this->assertRfcMatches($string, 'ATTRIBUTE_TYPE_AND_VALUE', $pieces);
    }

    /**
     * @dataProvider non__ATTRIBUTE_TYPE_AND_VALUE__cases
     */
    public function test__ATTRIBUTE_TYPE_AND_VALUE__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'ATTRIBUTE_TYPE_AND_VALUE');
    }

    //
    // NAME_COMPONENT
    //

    public static function NAME_COMPONENT__cases()
    {
        $cases = [
        ];
        $inheritedCases = [];
        foreach (self::ATTRIBUTE_TYPE_AND_VALUE__cases() as $first) {
            $inheritedCases[] = $first;
            foreach (self::ATTRIBUTE_TYPE_AND_VALUE__cases() as $second) {
                $case = [
                    $first[0].'+'.$second[0],
                ];
                $inheritedCases[] = $case;
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__NAME_COMPONENT__cases()
    {
        $strings = [
            '',
            '~',
            '-O',
            'O~',
            '1.',
            '=',
            '=asdf',
            'O = 1',
            'O=123+',
            'O=123+OU',
        ];
        return self::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider NAME_COMPONENT__cases
     */
    public function test__NAME_COMPONENT__matches(string $string, array $pieces =[])
    {
        $this->assertRfcMatches($string, 'NAME_COMPONENT', $pieces);
    }

    /**
     * @dataProvider non__NAME_COMPONENT__cases
     */
    public function test__NAME_COMPONENT__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'NAME_COMPONENT');
    }

    //
    // NAME
    //

    public static function NAME__cases()
    {
        $cases = [
        ];

        $secondLevelComponents = [
            '1.2.3=asdf',
            'foo-=#1234'
        ];

        $inheritedCases = [];
        foreach (self::NAME_COMPONENT__cases() as $first) {
            $inheritedCases[] = $first;
            foreach ($secondLevelComponents as $second) {
                $case = [
                    $first[0].','.$second
                ];
                $inheritedCases[] = $case;
            }
        }
        return array_merge($inheritedCases, $cases);
    }

    public static function non__NAME__cases()
    {
        $strings = [
            '',
            '~',
            '-O',
            'O~',
            '1.',
            '=',
            '=asdf',
            'O = 1',
            'O=123+',
            'O=123+OU',
            'O=123,',
            ',O=123',
        ];
        return self::stringsToPregTuples($strings);
    }

    /**
     * @dataProvider NAME__cases
     */
    public function test__NAME__matches(string $string, array $pieces =[])
    {
        $this->assertRfcMatches($string, 'NAME', $pieces);
    }

    /**
     * @dataProvider non__NAME__cases
     */
    public function test__NAME__notMatches(string $string)
    {
        $this->assertRfcNotMatches($string, 'NAME');
    }


    //
    // DISTINGUISHED_NAME
    //

    public function test__DISTINGUISHED_NAME()
    {
        $this->assertSame('(?<dn>'.Rfc2253::NAME.'?)', Rfc2253::DISTINGUISHED_NAME);
    }
}

// vim: syntax=php sw=4 ts=4 et:
