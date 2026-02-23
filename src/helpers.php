<?php

use TresorKasenda\Numberable\Numberable;

if (! function_exists('number')) {
    /**
     * Create a new Numberable instance — mirrors str() for numbers.
     */
    function number(int|float|null $value = null): Numberable|null
    {
        if (is_null($value)) {
            return null;
        }

        return new Numberable($value);
    }
}