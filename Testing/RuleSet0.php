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

use Korowai\Lib\Rfc\AbstractRuleSet;

/**
 * Sample class that extends AbstractRuleSet. Allows testing static methods of
 * the AbstractRuleSet.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class RuleSet0 extends AbstractRuleSet
{
    public const VAR_NAME = '(?<var_name>[[:alpha:]_]\w+)';

    /**
     * Returns an array of names of rules provided by this class.
     *
     * @return array
     */
    public static function getClassRuleNames() : array
    {
        return [
            'VAR_NAME',
        ];
    }

    /**
     * Returns what we expected the *getClassCaptures()* to return.
     *
     * @return array
     */
    public static function expectedClassCaptures() : array
    {
        return [
            'VAR_NAME' => ['var_name' => 'var_name'],
        ];
    }

    /**
     * Returns what we expect the *captures($ruleName)* to return.
     *
     * @return array
     */
    public static function expectedCaptures(string $ruleName) : array
    {
        return static::expectedClassCaptures()[$ruleName];
    }

    /**
     * Returns what we expect the *errorCaptures($ruleName)* to return.
     *
     * @return array
     */
    public static function expectedErrorCaptures(string $ruleName) : array
    {
        $captures = static::expectedCaptures($ruleName);
        return array_filter($captures, function (string $name) {
            return substr_compare(strtolower($name), 'error', -5) === 0;
        });
    }

    /**
     * Returns what we expect the *valueCaptures($ruleName)* to return.
     *
     * @return array
     */
    public static function expectedValueCaptures(string $ruleName) : array
    {
        $captures = static::expectedCaptures($ruleName);
        return array_filter($captures, function (string $name) {
            return substr_compare(strtolower($name), 'error', -5) !== 0;
        });
    }
}

// vim: syntax=php sw=4 ts=4 et:
