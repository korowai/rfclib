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

use Korowai\Lib\Rfc\Rule;
use Korowai\Lib\Rfc\RuleInterface;

/**
 * Functions useful for demonstration purposes.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
final class RuleDemo
{
    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * @var string
     */
    private $format;

    /**
     * Create demo for a *$rule* from given *$ruleSet*.
     *
     * @param  string $ruleSet Rule set class name.
     * @param  string $rule Rule name in the rule set class.
     * @param  string $format Used to generate actual regular expression from rule object.
     *
     * @return RuleDemo
     */
    public static function create(string $ruleSet, string $rule, string $format = null) : RuleDemo
    {
        return new self(new Rule($ruleSet, $rule), $format);
    }

    /**
     * Initializes the object.
     *
     * @param  RuleInterface $rule Rule object to be demonstrated.
     * @param  string $format Used to generate actual regular expression from $rule object.
     */
    public function __construct(RuleInterface $rule, string $format = null)
    {
        $this->setRule($rule);
        $this->setFormat($format ?? '/\G%s/D');
    }

    /**
     * Assigns new RuleInterface instance.
     *
     * @param  RuleInterface $rule
     * @return object $this
     */
    public function setRule(RuleInterface $rule)
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * Returns the RuleInterface instance assigned to this demo.
     *
     * @return RuleInterface
     */
    public function getRule() : RuleInterface
    {
        return $this->rule;
    }

    /**
     * Assigns new format string.
     *
     * @param  string $format
     * @return object $this
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Returns the format string used by this demo.
     *
     * @return string
     */
    public function getFormat() : string
    {
        return $this->format;
    }

    /**
     * Returns the regular expression as string.
     *
     * @return string
     */
    public function regex() : string
    {
        return sprintf($this->getFormat(), (string)$this->getRule());
    }

    /**
     * Matches *$subject* with ``preg_match`` and returns nice demonstrative report.
     *
     * @param  string $subject
     *      The subject string passed to ``preg_match()``.
     * @param  int $flags
     *      Flags passed to ``preg_match()`` (PREG_UNMATCHED_AS_NULL is added unconditionally).
     * @return string
     */
    public function matchAndGetReport(string $subject, int $flags = 0) : string
    {
        $flags |= PREG_UNMATCHED_AS_NULL;
        if (preg_match($this->regex(), $subject, $matches, $flags)) {
            $matches = static::filterCaptures($matches);
            $matches = json_encode($matches, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            return sprintf("matched: %s\nmatches: %s", static::quote($subject), $matches);
        } else {
            return sprintf("failed: %s", static::quote($subject));
        }
    }

    public function matchAndReport(string $subject, int $flags = 0)
    {
        printf("%s\n-\n", $this->matchAndGetReport($subject, $flags));
    }

    /**
     * Returns *$string* surrounded with *$quote* and with special characters
     * escaped.
     *
     * @param  string $string
     * @param  string $quote
     * @return string
     */
    public static function quote(string $string, string $quote = '"') : string
    {
        static $specials = [
            ["\n", '\n'],
            ["\r", '\r'],
            ["\t", '\t'],
            ["\v", '\v'],
            ["\e", '\e'],
            ["\f", '\f'],
            ["'" , "\\'"],
            ['"' , '\"'],
        ];
        [$search, $replace] = array_map(null, ...$specials);
        return $quote.str_replace($search, $replace, $string).$quote;
    }

    /**
     * Filters out empty capture groups from *$matches* and positional capture groups.
     *
     * @param  array $matches
     * @return array
     */
    public static function filterCaptures(array $matches) : array
    {
        return array_filter($matches, function ($m, $k) {
            return (is_array($m) ? $m[0] : $m) !== null && (is_string($k) || $k === 0);
        }, ARRAY_FILTER_USE_BOTH);
    }
}

// vim: syntax=php sw=4 ts=4 et:
