<?php

namespace Awuxtron\OptionsObject;

use ArrayAccess;
use Awuxtron\OptionsObject\Exceptions\UndefinedOptionsException;
use JsonSerializable;

/**
 * @implements ArrayAccess<mixed, mixed>
 */
abstract class OptionsObject implements ArrayAccess, JsonSerializable
{
    use ResolveClassAttributes;
    use Destroyable;
    use AccessAsArray;

    /**
     * The list of undeclared options.
     *
     * @var array<string, mixed>
     */
    protected array $options = [];

    final public function __construct()
    {
        $this->resolveAttributes();

        // Append default value to resolved options and unset.
        foreach (static::getResolvedOptions() as $name => $option) {
            if (!(!$option['nullable'] && $option['default'] === null)) {
                $this->options[$name] = $option['default'];
            }

            unset($this->{$name});
        }
    }

    /**
     * Get the option by any other method if the property of the option is not declared.
     *
     * @param string $name
     *
     * @throws UndefinedOptionsException
     *
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->options)) {
            if ($this->isResolved($name)) {
                return $this->getOptionValue($name, $this->options[$name], 'getters');
            }

            return $this->options[$name];
        }

        if ($this->isResolved($name) || !method_exists($this, $name) || $this->isDestroyed($name)) {
            throw new UndefinedOptionsException("The option '{$name}' does not exist.");
        }

        return $this->{$name}();
    }

    /**
     * Add new option if the name does not match any declared options.
     */
    public function __set(string $name, mixed $value): void
    {
        if ($this->isResolved($name)) {
            $value = $this->getOptionValue($name, $value);
        } elseif (method_exists($this, $name)) {
            $value = $this->{$name}($value);
        }

        $this->options[$name] = $value;

        $this->restoreOption($name);
    }

    /**
     * Determines the options is exists or not.
     */
    public function __isset(string $name): bool
    {
        if (property_exists($this, $name) || method_exists($this, $name)) {
            return true;
        }

        return array_key_exists($name, $this->options) || !$this->isDestroyed($name);
    }

    /**
     * Remove the option.
     */
    public function __unset(string $name): void
    {
        unset($this->options[$name]);

        $this->destroyOption($name);
    }

    /**
     * Create a new options object instance from an array of options.
     *
     * @param array<mixed> $options
     *
     * @return static
     */
    public static function of(array $options): static
    {
        return (new static)->replace($options);
    }

    /**
     * Replace options of this object from given options.
     *
     * @param array<string, mixed>|static $options
     *
     * @return static
     */
    public function replace(self|array $options = []): static
    {
        if ($options instanceof static) {
            return $options;
        }

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Converts the options object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $options = array_filter(array_replace(get_object_vars($this), $this->options), function ($key) {
            $ignores = [
                'reflectionClass',
                'propertyAttributes',
                'options',
                'destroyed',
            ];

            return !in_array($key, $ignores) && !$this->isDestroyed($key);
        }, ARRAY_FILTER_USE_KEY);

        $arr = array_map(fn ($value) => $value instanceof self ? $value->toArray() : $value, $options);

        foreach ($arr as $key => $val) {
            if (!$this->isResolved($key)) {
                continue;
            }

            $arr[$key] = $this->getOptionValue($key, $val, 'getters');
        }

        return $arr;
    }

    /**
     * Converts the options object to json string.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
