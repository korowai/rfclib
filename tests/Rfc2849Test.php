<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Lib\Rfc;

use Korowai\Lib\Rfc\Rfc2849;
use Korowai\Lib\Rfc\AbstractRuleSet;
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

    public function test__extends__AbstractRuleSet()
    {
        $this->assertExtendsClass(AbstractRuleSet::class, $this->getRfcClass());
    }

    public function test__getClassRuleNames()
    {
        $class = self::getRfcClass();
        $this->assertSame(array_keys(self::findRfcConstants()), $class::getClassRuleNames());
    }

    public function test__getDefinedErrors()
    {
        $class = self::getRfcClass();
        $rfc2849Errors = [
            '' => [
                'SEP'                   => 'expected line separator (RFC2849)',
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
        $this->assertSame($rfc2849Errors, $class::getDefinedErrors());
    }
}

// vim: syntax=php sw=4 ts=4 et:
