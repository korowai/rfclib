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
 * Sample class that extends AbstractRuleSet. Allows testing static methods of
 * the AbstractRuleSet.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class RuleSet1 extends RuleSet0
{
    public const INT = '(?:(?:[+-]\s*)?[0-9]+)';
    public const PARTIAL_INT = '(?:(?:[+-]\s*)?[0-9]*)';
    public const ASSIGNMENT_INT =
        '(?:'.
            self::VAR_NAME.'\s*=\s*'.
            '(?:'.
                '(?:(?<value_int>'.self::INT.')\s*;)'.
                '|'.
                '(?:'.self::PARTIAL_INT.'(?<value_int_error>[^;]*))'.
            ')'.
        ')';

    /**
     * {@inheritdoc}
     */
    public static function getClassRuleNames() : array
    {
        $rules = [
            'INT',
            'PARTIAL_INT',
            'ASSIGNMENT_INT',
        ];
        return array_merge($rules, parent::getClassRuleNames());
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefinedErrors() : array
    {
        $errors = [
            '' => [
                'ASSIGNMENT_INT' => 'missing "var_name =" in integer assignment',
            ],
            'value_int_error' => 'malformed integer value',
        ];
        return array_merge(parent::getDefinedErrors(), $errors);
    }

    /**
     * Returns what we expected the *getClassCaptures()* to return.
     *
     * @return array
     */
    public static function expectedClassCaptures() : array
    {
        $captures = [
            'INT' => [],
            'PARTIAL_INT' => [],
            'ASSIGNMENT_INT' => [
                'var_name' => 'var_name',
                'value_int' => 'value_int',
                'value_int_error' => 'value_int_error'
            ],
        ];
        return array_merge(parent::expectedClassCaptures(), $captures);
    }
}

// vim: syntax=php sw=4 ts=4 et:
