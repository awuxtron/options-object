<?php

namespace Awuxtron\OptionsObject\Attributes;

use Awuxtron\OptionsObject\OptionsObject;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractAttribute
{
    /**
     * The instance contains this attribute.
     *
     * @var OptionsObject
     */
    protected OptionsObject $instance;

    /**
     * The reflection class contains this attribute.
     *
     * @var ReflectionClass<OptionsObject>
     */
    protected ReflectionClass $reflectionClass;

    /**
     * The reflection property instance of the attribute.
     */
    protected ReflectionProperty $reflectionProperty;

    /**
     * Get the option getter.
     *
     * @return callable(mixed): mixed
     */
    public function getReceiver(): callable
    {
        return fn ($value) => $value;
    }

    /**
     * Get the option setter.
     *
     * @return callable(mixed): mixed
     */
    public function getSetter(): callable
    {
        return fn ($value) => $value;
    }

    /**
     * Transform the default value of option.
     */
    public function transformDefaultValue(mixed $default, bool $allowsNull): mixed
    {
        return $default;
    }

    /**
     * Get the option name.
     */
    public function getOptionName(): string
    {
        return $this->reflectionProperty->getName();
    }

    /**
     * The instance contains this attribute.
     */
    public function setInstance(OptionsObject $instance): static
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Set the reflection class contains this attribute.
     *
     * @param ReflectionClass<OptionsObject> $class
     *
     * @return static
     */
    public function setReflectionClass(ReflectionClass $class): static
    {
        $this->reflectionClass = $class;

        return $this;
    }

    /**
     * Set the reflection property instance of the attribute.
     */
    public function setProperty(ReflectionProperty $property): static
    {
        $this->reflectionProperty = $property;

        return $this;
    }

    /**
     * Bootstrap the attribute, this function will call after instance and reflection class is settled.
     */
    public function boot(): void
    {
    }
}
