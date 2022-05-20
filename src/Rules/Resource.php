<?php

namespace Awuxtron\OptionsObject\Rules;

class Resource implements Rule
{
    /**
     * Checks if the given value is valid resource.
     */
    public function __invoke(mixed $value): bool
    {
        return assert(is_resource($value), 'The given value must be of type resource.');
    }
}
