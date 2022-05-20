<?php

namespace Awuxtron\OptionsObject\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class AliasOf extends AbstractAttribute
{
    public function __construct(protected string $reference)
    {
    }

    /**
     * Get the option getter.
     *
     * @return callable(mixed): mixed
     */
    public function getReceiver(): callable
    {
        return fn () => $this->instance[$this->reference];
    }

    /**
     * Get the option setter.
     *
     * @return callable(mixed): mixed
     */
    public function getSetter(): callable
    {
        return fn ($value) => $this->instance[$this->reference] = $value;
    }

    /**
     * Transform the default value of option.
     */
    public function transformDefaultValue(mixed $default, bool $allowsNull): mixed
    {
        if ($default === null) {
            return null;
        }

        return $this->instance[$this->reference];
    }
}
