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
 * Abstract base class for korowai/rfclib unit tests.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
abstract class TestCase extends \Korowai\Testing\TestCase
{
    /**
     * Returns the name of RFC class being tested.
     *
     * @return string
     */
    abstract public static function getRfcClass() : string;

    /**
     * {@inheritdoc}
     */
    public static function objectPropertyGettersMap() : array
    {
        return array_merge_recursive(
            parent::objectPropertyGettersMap(),
            \Korowai\Testing\Contracts\ObjectPropertyGettersMap::getObjectPropertyGettersMap(),
            \Korowai\Testing\Rfclib\ObjectPropertyGettersMap::getObjectPropertyGettersMap()
        );
    }

    /**
     * Returns the fully qualified name of RFC constant being tested.
     *
     * @return string
     */
    public static function getRfcFqdnConstName(string $constname) : string
    {
        return (static::getRfcClass()).'::'.$constname;
    }

    /**
     * Returns full PCRE expression for an expression stored in RFC constant.
     *
     * @param  string $fqdnConstName
     * @param  array $options
     *
     * @return string
     */
    public static function getRfcRegexp(string $fqdnConstName, array $options = [])
    {
        $prefix = $options['prefix'] ?? '/^';
        $suffix = $options['suffix'] ?? '$/D';
        return $prefix.constant($fqdnConstName).$suffix;
    }

    /**
     * Asserts that an expression stored in an RFC constant (*$constname*)
     * matches the *$subject*. *$expMatches* may be provided to perform
     * additional checks on *$matches* returned by ``preg_match()``.
     *
     * @param  string $subject
     * @param  string $constname
     * @param  array $expMatches
     * @param  array $options
     */
    public static function assertRfcMatches(
        string $subject,
        string $constname,
        array $expMatches = [],
        array $options = []
    ) : void {
        $fqdnConstName = static::getRfcFqdnConstName($constname);
        $re = static::getRfcRegexp($fqdnConstName, $options);
        $result = preg_match($re, $subject, $matches, PREG_UNMATCHED_AS_NULL|PREG_OFFSET_CAPTURE);
        $msg = 'Failed asserting that '.$fqdnConstName.' matches '.var_export($subject, true);
        static::assertSame(1, $result, $msg);
        static::assertHasPregCaptures($expMatches, $matches);
    }

    /**
     * Asserts that an expression stored in an RFC constant (*$constname*)
     * does not match the *$subject*.
     *
     * @param  string $subject
     * @param  string $constname
     * @param  array $options
     */
    public static function assertRfcNotMatches(string $subject, string $constname, array $options = []) : void
    {
        $fqdnConstName = static::getRfcFqdnConstName($constname);
        $re = static::getRfcRegexp($fqdnConstName, $options);
        $result = preg_match($re, $subject);
        $msg = 'Failed asserting that '.$fqdnConstName.' does not match '.var_export($subject, true);
        static::assertSame(0, $result, $msg);
    }

    /**
     * Gets all defined constants from the tested Rfc class.
     *
     * @return An array of constants of the tested Rfc class, where the keys
     *         hold the name and the values the value of the constants.
     */
    public static function findRfcConstants() : array
    {
        $class = new \ReflectionClass(static::getRfcClass());
        return $class->getConstants();
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $constants An array with names of Rfc constants.
     * @param  string $nameRe Regular expression used to match names of the capture groups.
     * @return array
     */
    public static function findRfcCaptures(array $constants = null, string $nameRe = '\w+') : array
    {
        $constantValues = static::findRfcConstants();
        if ($constants === null) {
            $constants = array_keys($constantValues);
        }

        $re = '/\(\?P?<(?<list>'.$nameRe.')>/';
        return array_map(function (string $key) use ($constantValues, $re) {
            $value = $constantValues[$key];
            preg_match_all($re, $value, $matches);
            return array_combine($matches['list'], $matches['list']);
        }, array_combine($constants, $constants));
    }
}

// vim: syntax=php sw=4 ts=4 et:
