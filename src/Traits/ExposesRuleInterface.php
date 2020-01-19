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

use Korowai\Lib\Rfc\RuleInterface;

/**
 * @todo Write documentation
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait ExposesRuleInterface
{
    /**
     * Returns the instance of [RuleInterface](\.\./RuleInterface.html)
     * encapsulated by this object or null.
     *
     * @return RuleInterface|null
     */
    abstract public function getRfcRule() : ?RuleInterface;

    /**
     * Returns the regular expression that implements the rule. Same as
     * *$this->rule()*.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->getRfcRule()->__toString();
    }

    /**
     * Returns the regular expression that implements the rule.
     *
     * @return string
     */
    public function regexp() : string
    {
        return $this->getRfcRule()->regexp();
    }

    /**
     * Returns an array of capture group names for the rule.
     *
     * @return array Array of captures.
     */
    public function captures() : array
    {
        return $this->getRfcRule()->captures();
    }

    /**
     * Returns an array of error-catching capture group names (*errorKey*s) for
     * the rule.
     *
     * @return array Array of error-catching captures.
     */
    public function errorCaptures() : array
    {
        return $this->getRfcRule()->errorCaptures();
    }

    /**
     * Returns an array of non-error capture group names for the rule.
     *
     * @return array Array of non-error captures.
     */
    public function valueCaptures() : array
    {
        return $this->getRfcRule()->valueCaptures();
    }

    /**
     * Returns an array containing all entries of *$matches* which have keys
     * that are present in *errorCaptures()*.
     *
     * @param  array $matches
     * @return array
     */
    public function findCapturedErrors(array $matches) : array
    {
        return $this->getRfcRule()->findCapturedErrors($matches);
    }

    /**
     * Returns an array containing all entries of *$matches* which have keys
     * that are present in *valueCaptures($ruleName)*.
     *
     * @param  array $matches
     * @return array
     */
    public function findCapturedValues(array $matches) : array
    {
        return $this->getRfcRule()->findCapturedValues($matches);
    }

    /**
     * Returns error message for given *$errorKey* (or for the whole unmatched
     * rule, without *$errorKey*).
     *
     * @param  string $errorKey
     * @return array
     */
    public function getErrorMessage(string $errorKey = '') : string
    {
        return $this->getRfcRule()->getErrorMessage($errorKey);
    }
}

// vim: syntax=php sw=4 ts=4 et:
