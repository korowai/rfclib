<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Rfc\Traits;

/**
 * Implements StaticRuleSetInterface assuming that rules are provided as class
 * constants. The receiving class shall implement four methods:
 *
 * - *getClassRuleNames()*,
 * - *getClassCaptures()*,
 * - *setClassCaptures()*,
 * - *isErrorCapture()*,
 *
 * as specified below.
 */
trait RulesFromConstants
{
    /**
     * Returns an array of names of rules provided by this class.
     *
     * @return array
     */
    abstract public static function getClassRuleNames() : array;

    /**
     * Returns the array of captures for current class.
     *
     * @return array|null
     *      The array of captures for the class (or null) as assigned with
     *      *setClassCaptures()*.
     */
    abstract protected static function getClassCaptures() : ?array;

    /**
     * Assigns *$captures* to the internal array of captures for current class.
     *
     * @param  array $captures
     */
    abstract protected static function setClassCaptures(array $captures) : void;

    /**
     * Returns true, if capture group with name *$name* is an error-catching
     * capture group.
     *
     * @param  string $name
     * @return bool
     */
    abstract public static function isErrorCapture(string $name) : bool;

    /**
     * Returns the regular expression that implements given rule.
     *
     * @param  string $ruleName
     * @return array
     */
    public static function regexp(string $ruleName) : string
    {
        return constant(static::class.'::'.$ruleName);
    }

    /**
     * Returns an array where keys are rule names and values are regular
     * expressions that implement these rules.
     *
     * @return array
     */
    public static function rules() : array
    {
        $ruleNames = static::getClassRuleNames();
        return array_combine($ruleNames, array_map([static::class, 'regexp'], $ruleNames));
    }

    /**
     * Returns an array of capture group names for given rule.
     *
     * @param  string $ruleName Name of the rule (a constant containing regular expression).
     * @return array Array of captures.
     */
    public static function captures(string $ruleName) : array
    {
        if (($captures = static::getClassCaptures()) === null) {
            $rules = static::rules();
            static::setClassCaptures($captures = static::findCaptures($rules));
        }
        return $captures[$ruleName];
    }

    /**
     * Returns an array of error-catching capture group names for given rule.
     *
     * @param  string $ruleName Name of the rule.
     * @return array Array of error-catching captures.
     */
    public static function errorCaptures(string $ruleName) : array
    {
        $captures = static::captures($ruleName);
        return array_filter($captures, function (string $name) {
            return static::isErrorCapture($name);
        });
    }

    /**
     * Returns an array of non-error capture group names for given rule.
     *
     * @param  string $ruleName Name of the rule.
     * @return array Array of non-error captures.
     */
    public static function valueCaptures(string $ruleName) : array
    {
        $captures = static::captures($ruleName);
        return array_filter($captures, function (string $name) {
            return !static::isErrorCapture($name);
        });
    }

    /**
     * Scans rules' expressions for named capture groups and returns an array
     * that maps rule names onto arrays of capture group names.
     *
     * @param  array $rules
     *      An array of rules with rule names as keys and corresponding regular
     *      expressions as values.
     * @param  string $nameRegex
     *      Regular expression used to match the capture goups' names.
     * @return array
     */
    protected static function findCaptures(array $rules, string $nameRegex = '[[:alpha:]_]\w*') : array
    {
        $captures = [];
        foreach ($rules as $rule => $subject) {
            preg_match_all('/\(\?P?<(?<captures>'.$nameRegex.')>/D', $subject, $matches);
            $captures[$rule] = array_combine($matches['captures'], $matches['captures']);
        }
        return $captures;
    }
}

// vim: syntax=php sw=4 ts=4 et:
