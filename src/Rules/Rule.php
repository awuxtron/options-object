<?php

namespace Awuxtron\OptionsObject\Rules;

interface Rule
{
    /**
     * Checks if the given value is valid.
     */
    public function __invoke(mixed $value): bool;
}
