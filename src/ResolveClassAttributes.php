<?php

namespace Awuxtron\OptionsObject;

use Awuxtron\OptionsObject\Attributes\AbstractAttribute;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

trait ResolveClassAttributes
{
    /**
     * Determines all attributes are resolved or not.
     *
     * @var array<class-string<static>, bool>
     */
    protected static array $isResolved = [];

    /**
     * An array of resolved options.
     *
     * @var array<class-string<static>, array<string, array{default: mixed, nullable: bool, setters: callable[], getters: callable[], has_default: bool}>>
     */
    protected static array $resolvedOptions = [];

    /**
     * The reflection class instance of current class.
     *
     * @var ReflectionClass<static>
     */
    protected ReflectionClass $reflectionClass;

    /**
     * An array of property attributes.
     *
     * @var array<string, ReflectionAttribute<AbstractAttribute>[]>
     */
    protected array $propertyAttributes = [];

    /**
     * Get all resolved options.
     *
     * @return array<string, array{default: mixed, nullable: bool, setters: callable[], getters: callable[], has_default: bool}>
     */
    protected static function getResolvedOptions(): array
    {
        return static::$resolvedOptions[static::class] ?? [];
    }

    /**
     * Resolve all unresolved attributes.
     */
    protected function resolveAttributes(): void
    {
        if (static::$isResolved[static::class] ?? false) {
            return;
        }

        if (!isset($this->reflectionClass)) {
            $this->reflectionClass = new ReflectionClass($this);
        }

        // Resolve all attributes contains in this class.
        foreach ($this->getProperties() as $property) {
            $resolved = [
                'default' => $property->getDefaultValue(),
                'has_default' => $property->hasDefaultValue(),
                'nullable' => (bool) $property->getType()?->allowsNull(),
                'setters' => [],
                'getters' => [],
            ];

            foreach ($this->getPropertyAttributes($property) as $reflectionAttribute) {
                $attribute = $this->createAttributeInstance($property, $reflectionAttribute);

                $resolved['setters'][] = $attribute->getSetter();
                $resolved['getters'][] = $attribute->getReceiver();
                $resolved['default'] = $attribute->transformDefaultValue($resolved['default'], $resolved['nullable']);
            }

            static::$resolvedOptions[static::class][$property->getName()] = $resolved;
        }

        static::$isResolved[static::class] = true;
    }

    /**
     * Create new attribute instance from reflection attribute instance.
     *
     * @param ReflectionProperty                     $property
     * @param ReflectionAttribute<AbstractAttribute> $attribute
     *
     * @return AbstractAttribute
     */
    protected function createAttributeInstance(ReflectionProperty $property, ReflectionAttribute $attribute): AbstractAttribute
    {
        $instance = $attribute->newInstance();

        $instance->setInstance($this);
        $instance->setReflectionClass($this->reflectionClass);
        $instance->setProperty($property);
        $instance->boot();

        return $instance;
    }

    /**
     * Checks if the option is resolved.
     */
    protected function isResolved(string $name): bool
    {
        return isset(static::$resolvedOptions[static::class][$name]);
    }

    /**
     * Transform the option value by setter or getter defined in attribute.
     */
    protected function getOptionValue(string $name, mixed $value, string $type = 'setters'): mixed
    {
        return array_reduce(static::$resolvedOptions[static::class][$name][$type], function ($carry, $item) {
            return $item($carry);
        }, $value);
    }

    /**
     * Get all properties contains any valid attributes.
     *
     * @return ReflectionProperty[]
     */
    protected function getProperties(): array
    {
        return array_filter(
            $this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC),
            function (ReflectionProperty $property) {
                return !empty($this->getPropertyAttributes($property));
            }
        );
    }

    /**
     * Get all attributes instance of {@see AbstractAttribute} declared in the property.
     *
     * @param ReflectionProperty $property
     *
     * @return ReflectionAttribute<AbstractAttribute>[]
     */
    protected function getPropertyAttributes(ReflectionProperty $property): array
    {
        if (!isset($this->propertyAttributes[$name = $property->getName()])) {
            $this->propertyAttributes[$name] = $property->getAttributes(
                AbstractAttribute::class,
                ReflectionAttribute::IS_INSTANCEOF
            );
        }

        return $this->propertyAttributes[$name];
    }
}
