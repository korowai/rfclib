<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Testing\Rfclib;

/**
 * Defines getters map for properties of objects from korowai/rfclib
 * package.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ObjectPropertyGettersMap
{
    /**
     * Object property getters per class for korowai/rfclib package.
     *
     * @var array
     */
    protected static $rfclibObjectPropertyGettersMap = [
        \Korowai\Lib\Rfc\AbstractRuleSet::class => [
            'classCaptures'         => 'getClassCaptures',
            'definedErrors'         => 'getDefinedErrors',
        ],
        \Korowai\Lib\Rfc\Exception\InvalidRuleSetNameException::class => [
        ],
        \Korowai\Lib\Rfc\Rfc2253::class => [
        ],
        \Korowai\Lib\Rfc\Rfc2849::class => [
        ],
        \Korowai\Lib\Rfc\Rfc3986::class => [
        ],
        \Korowai\Lib\Rfc\Rfc5234::class => [
        ],
        \Korowai\Lib\Rfc\Rfc8089::class => [
        ],
        \Korowai\Lib\Rfc\Rule::class => [
            'name'                  => 'name',
            'ruleSetClass'          => 'ruleSetClass',
        ],
        \Korowai\Lib\Rfc\Traits\DecoratesRuleInterface::class => [
            'rfcRule'               => 'getRfcRule'
        ],
        \Korowai\Lib\Rfc\Traits\ExposesRuleInterface::class => [
        ],
        \Korowai\Lib\Rfc\Traits\RulesFromConstants::class => [
        ],
        \Korowai\Testing\Rfclib\RuleDemo::class => [
            'rule'                  => 'getRule',
            'format'                => 'getFormat',
            'regex'                 => 'regex'
        ]
    ];

    /**
     * Returns the property getters map.
     *
     * @return array
     */
    public static function getObjectPropertyGettersMap() : array
    {
        return self::$rfclibObjectPropertyGettersMap;
    }
}

// vim: syntax=php sw=4 ts=4 et:
