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
class RuleSet2 extends RuleSet1
{
    public const STRING = '(?:"(?:[^"]|\\")*")';
    public const PARTIAL_STRING = '(?:"(?:[^"]|\\")*)';
    public const ASSIGNMENT_STRING =
        '(?:'.
            self::VAR_NAME.'\s*=\s*'.
            '(?:'.
                '(?:(?<value_string>'.self::STRING.');)'.
                '|'.
                '(?:'.self::PARTIAL_STRING.'(?<value_string_error>[^;]*))'.
            ')'.
        ')';

    /**
     * Returns an array of names of rules provided by this class.
     *
     * @return array
     */
    public static function getClassRuleNames() : array
    {
        $rules = [
            'STRING',
            'PARTIAL_STRING',
            'ASSIGNMENT_STRING',
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
                'ASSIGNMENT_STRING' => 'missing "var_name =" in string assignment'
            ],
            'value_int_error' => [
                'ASSIGNMENT_INT' => 'malformed integer in assignment',
            ],
            'value_string_error' => [
                'malformed string',
                'ASSIGNMENT_STRING' => 'malformed string in assignment'
            ],
        ];
        return array_merge_recursive(parent::getDefinedErrors(), $errors);
    }

    /**
     * Returns what we expected the *getClassCaptures()* to return.
     *
     * @return array
     */
    public static function expectedClassCaptures() : array
    {
        $captures = [
            'STRING' => [],
            'PARTIAL_STRING' => [],
            'ASSIGNMENT_STRING' => [
                'var_name' => 'var_name',
                'value_string' => 'value_string',
                'value_string_error' => 'value_string_error'
            ],
        ];
        return array_merge(parent::expectedClassCaptures(), $captures);
    }
}

// vim: syntax=php sw=4 ts=4 et:
