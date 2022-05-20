<?php

namespace Awuxtron\OptionsObject\Attributes;

use Attribute;
use Awuxtron\OptionsObject\Rules\Rule;
use InvalidArgumentException;

/**
 * Validate the value when set the option value.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Validate extends AbstractAttribute
{
    /**
     * The list of validation rules.
     *
     * @var class-string<Rule>[]
     */
    protected array $rules;

    /**
     * @phpstan-param class-string<Rule> $rules
     *
     * @param string ...$rules
     */
    public function __construct(string ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * Get the option setter.
     *
     * @return callable(mixed): mixed
     */
    public function getSetter(): callable
    {
        return function (mixed $value) {
            foreach ($this->rules as $rule) {
                $isValid = (new $rule)($value);

                if (!$isValid) {
                    throw new InvalidArgumentException("{$rule}: The given value is invalid.");
                }
            }

            return $value;
        };
    }
}
