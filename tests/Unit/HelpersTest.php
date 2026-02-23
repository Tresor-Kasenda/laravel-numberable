<?php

use TresorKasenda\Numberable\Numberable;

/*
|--------------------------------------------------------------------------
| number() Helper Function
|--------------------------------------------------------------------------
*/

test('number() returns a Numberable instance', function () {
    $result = number(42);

    expect($result)->toBeInstanceOf(Numberable::class)
        ->and($result->value())->toBe(42);
});

test('number() with float returns a Numberable instance', function () {
    $result = number(3.14);

    expect($result)->toBeInstanceOf(Numberable::class)
        ->and($result->value())->toBe(3.14);
});

test('number() with null returns null', function () {
    $result = number(null);

    expect($result)->toBeNull();
});

test('number() returns a chainable Numberable', function () {
    $result = number(10)->add(5)->multiply(2)->value();

    expect($result)->toBe(30);
});

test('number() formatting works', function () {
    $result = (string) number(42);

    expect($result)->toBe('42');
});

test('number() with zero returns a Numberable, not null', function () {
    $result = number(0);

    expect($result)->toBeInstanceOf(Numberable::class)
        ->and($result->value())->toBe(0);
});

test('number() with negative value', function () {
    $result = number(-100);

    expect($result)->toBeInstanceOf(Numberable::class)
        ->and($result->value())->toBe(-100);
});
