<?php

namespace TresorKasenda\Numberable\Concerns;

trait Dumpable
{
    public function dump(...$args): static
    {
        $values = $args !== [] ? $args : [$this];

        if (function_exists('dump')) {
            dump(...$values);
        } else {
            foreach ($values as $value) {
                var_dump($value);
            }
        }

        return $this;
    }

    public function dd(...$args): never
    {
        $values = $args !== [] ? $args : [$this];

        if (function_exists('dd')) {
            dd(...$values);
        }

        if (function_exists('dump')) {
            dump(...$values);
        } else {
            foreach ($values as $value) {
                var_dump($value);
            }
        }

        exit(1);
    }
}
