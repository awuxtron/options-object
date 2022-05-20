<?php

namespace Awuxtron\OptionsObject\Attributes;

use Attribute;
use Awuxtron\OptionsObject\OptionsObject;
use InvalidArgumentException;

/**
 * Mark the current option is an option object.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class AsOptionsObject extends AbstractAttribute
{
    /**
     * @param string $target the option target
     *
     * @phpstan-param class-string<OptionsObject> $target
     */
    public function __construct(protected string $target)
    {
    }

    /**
     * Get the option setter.
     *
     * @return callable(mixed): mixed
     */
    public function getSetter(): callable
    {
        return function (OptionsObject|array|null $value): ?OptionsObject {
            if ($value instanceof OptionsObject) {
                return $value;
            }

            if ($value === null) {
                $type = $this->reflectionProperty->getType();

                if (!$type || !$type->allowsNull()) {
                    throw new InvalidArgumentException(
                        "The option '{$this->getOptionName()}' does not accept null as value."
                    );
                }

                return null;
            }

            return (new $this->target)->replace($value);
        };
    }

    /**
     * Transform the default value of option.
     */
    public function transformDefaultValue(mixed $default, bool $allowsNull): ?OptionsObject
    {
        if ($default === null && $allowsNull) {
            return null;
        }

        if ($default instanceof OptionsObject) {
            return $default;
        }

        if (!is_array($default)) {
            return null;
        }

        return (new $this->target)->replace($default);
    }
}
