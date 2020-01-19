<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Lib\Rfc\Traits;

use Korowai\Lib\Rfc\Traits\DecoratesRuleInterface;
use Korowai\Lib\Rfc\Traits\ExposesRuleInterface;
use Korowai\Lib\Rfc\RuleInterface;
use Korowai\Testing\TestCase;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class DecoratesRuleInterfaceTest extends TestCase
{
    public function test__uses__ExposesRuleInterface()
    {
        $this->assertUsesTrait(ExposesRuleInterface::class, DecoratesRuleInterface::class);
    }

    public function test__rfcRule()
    {
        $rule = $this->getMockBuilder(RuleInterface::class)
                     ->getMockForAbstractClass();

        $obj = new class implements RuleInterface {
            use DecoratesRuleInterface;
        };

        $this->assertNull($obj->getRfcRule());
        $this->assertSame($obj, $obj->setRfcRule($rule));
        $this->assertSame($rule, $obj->getRfcRule());
        $this->assertSame($obj, $obj->setRfcRule(null));
        $this->assertNull($obj->getRfcRule());
    }
}

// vim: syntax=php sw=4 ts=4 et:
