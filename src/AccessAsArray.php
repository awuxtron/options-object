<?php

namespace Awuxtron\OptionsObject;

trait AccessAsArray
{
    public function offsetGet(mixed $offset): mixed
    {
        return $this->dot($offset, fn ($carry, $key) => $carry->{$key});
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->dot($offset, function ($carry, $key, $last) use ($value) {
            if (!$last) {
                return $carry->{$key};
            }

            return $carry->{$key} = $value;
        });
    }

    public function offsetExists(mixed $offset): bool
    {
        $obj = $this;

        foreach (explode('.', $offset) as $key) {
            if (isset($obj->{$key})) {
                $obj = $obj->{$key};

                continue;
            }

            return false;
        }

        return true;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->dot($offset, function ($carry, $key, $last) {
            if (!$last) {
                return $carry->{$key};
            }

            unset($carry->{$key});

            return null;
        });
    }

    protected function dot(string $key, callable $callback): mixed
    {
        $keys = explode('.', $key);
        $last = end($keys);

        return array_reduce($keys, fn ($carry, $item) => $callback($carry, $item, $item == $last), $this);
    }
}
