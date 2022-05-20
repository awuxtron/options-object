<?php

namespace Awuxtron\OptionsObject;

trait Destroyable
{
    /**
     * The list of destroyed options.
     *
     * @var array<string, bool>
     */
    protected array $destroyed = [];

    /**
     * Destroy the option.
     */
    protected function destroyOption(string $name): void
    {
        $this->destroyed[$name] = true;
    }

    /**
     * Restore the option from destroyed list.
     */
    protected function restoreOption(string $name): void
    {
        unset($this->destroyed[$name]);
    }

    /**
     * Checks if the option is destroyed.
     */
    protected function isDestroyed(string $name): bool
    {
        return array_key_exists($name, $this->destroyed);
    }
}
