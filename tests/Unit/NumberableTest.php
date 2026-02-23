<?php

use TresorKasenda\Numberable\Numberable;

test('make() creates a Numberable instance with an integer', function () {
    $number = Numberable::make(42);

    expect($number)
        ->toBeInstanceOf(Numberable::class)
        ->value()->toBe(42);
});

test('make() creates a Numberable instance with a float', function () {
    $number = Numberable::make(3.14);

    expect($number)
        ->toBeInstanceOf(Numberable::class)
        ->value()->toBe(3.14);
});

test('make() accepts zero', function () {
    expect(Numberable::make(0)->value())->toBe(0);
});

test('make() accepts negative values', function () {
    expect(Numberable::make(-99)->value())->toBe(-99);
});

test('parse() converts a numeric string to a float', function () {
    $number = Numberable::parse('42.5');

    expect($number->value())->toBe(42.5);
});

test('parse() handles comma as decimal separator', function () {
    $number = Numberable::parse('1234,56');

    expect($number->value())->toBe(1234.56);
});

test('parse() accepts an integer directly', function () {
    $number = Numberable::parse(100);

    expect($number->value())->toBe(100);
});

test('parse() accepts a float directly', function () {
    $number = Numberable::parse(9.99);

    expect($number->value())->toBe(9.99);
});

test('parse() with locale parses a localized string', function () {
    $number = Numberable::parse('1.234,56', 'de_DE');

    expect($number->value())->toBeFloat();
});

test('parseInt() creates an integer Numberable from a string', function () {
    $number = Numberable::parseInt('42');

    expect($number->value())->toBe(42)
        ->and($number->value())->toBeInt();
});

test('parseInt() truncates float strings', function () {
    $number = Numberable::parseInt('42.9');

    expect($number->value())->toBe(42);
});

test('parseFloat() creates a float Numberable from a string', function () {
    $number = Numberable::parseFloat('3.14');

    expect($number->value())->toBe(3.14)
        ->and($number->value())->toBeFloat();
});

test('fluent methods return new instances (immutability)', function () {
    $original = Numberable::make(10);
    $modified = $original->add(5);

    expect($original->value())->toBe(10)
        ->and($modified->value())->toBe(15)
        ->and($original)->not->toBe($modified);
});

test('withLocale() does not mutate the original', function () {
    $original = Numberable::make(1000);
    $localized = $original->withLocale('fr');

    expect($original->format())->not->toContain('€')
        ->and($localized)->not->toBe($original);
});

test('withCurrency() does not mutate the original', function () {
    $original = Numberable::make(100);
    $withCurrency = $original->withCurrency('EUR');

    expect($original)->not->toBe($withCurrency);
});

test('withPrecision() does not mutate the original', function () {
    $original = Numberable::make(3.14159);
    $precise = $original->withPrecision(2);

    expect($original)->not->toBe($precise);
});

/*
|--------------------------------------------------------------------------
| Arithmetic Operations
|--------------------------------------------------------------------------
*/

test('add() sums correctly', function () {
    $result = Numberable::make(10)->add(5);

    expect($result->value())->toBe(15);
});

test('add() works with floats', function () {
    $result = Numberable::make(1.5)->add(2.3);

    expect($result->value())->toBe(3.8);
});

test('subtract() subtracts correctly', function () {
    $result = Numberable::make(10)->subtract(3);

    expect($result->value())->toBe(7);
});

test('subtract() can produce negative values', function () {
    $result = Numberable::make(5)->subtract(10);

    expect($result->value())->toBe(-5);
});

test('multiply() multiplies correctly', function () {
    $result = Numberable::make(6)->multiply(7);

    expect($result->value())->toBe(42);
});

test('multiply() works with zero', function () {
    $result = Numberable::make(100)->multiply(0);

    expect($result->value())->toBe(0);
});

test('divide() divides correctly', function () {
    $result = Numberable::make(42)->divide(6);

    expect($result->value())->toEqual(7);
});

test('divide() by zero throws DivisionByZeroError', function () {
    Numberable::make(10)->divide(0);
})->throws(\DivisionByZeroError::class, 'Division by zero.');

test('divide() by float zero throws DivisionByZeroError', function () {
    Numberable::make(10)->divide(0.0);
})->throws(\DivisionByZeroError::class, 'Division by zero.');

test('mod() returns modulo', function () {
    $result = Numberable::make(10)->mod(3);

    expect($result->value())->toBe(1.0);
});

test('pow() raises to the given exponent', function () {
    $result = Numberable::make(2)->pow(8);

    expect($result->value())->toBe(256);
});

test('pow() with fractional exponent (square root)', function () {
    $result = Numberable::make(9)->pow(0.5);

    expect($result->value())->toBe(3.0);
});

test('abs() returns absolute value of a negative number', function () {
    $result = Numberable::make(-42)->abs();

    expect($result->value())->toBe(42);
});

test('abs() leaves positive values unchanged', function () {
    $result = Numberable::make(42)->abs();

    expect($result->value())->toBe(42);
});

test('round() rounds half up by default', function () {
    $result = Numberable::make(2.5)->round();

    expect($result->value())->toBe(3.0);
});

test('round() with precision', function () {
    $result = Numberable::make(3.14159)->round(2);

    expect($result->value())->toBe(3.14);
});

test('floor() rounds down', function () {
    $result = Numberable::make(4.9)->floor();

    expect($result->value())->toBe(4.0);
});

test('floor() with negative numbers', function () {
    $result = Numberable::make(-4.1)->floor();

    expect($result->value())->toBe(-5.0);
});

test('ceil() rounds up', function () {
    $result = Numberable::make(4.1)->ceil();

    expect($result->value())->toBe(5.0);
});

test('ceil() with negative numbers', function () {
    $result = Numberable::make(-4.9)->ceil();

    expect($result->value())->toBe(-4.0);
});

test('arithmetic operations can be chained fluently', function () {
    $result = Numberable::make(10)
        ->add(5)
        ->multiply(2)
        ->subtract(10)
        ->divide(2);

    expect($result->value())->toEqual(10);
});

test('isInt() returns true for integers', function () {
    expect(Numberable::make(42)->isInt())->toBeTrue();
});

test('isInt() returns true for floats with zero fractional part', function () {
    expect(Numberable::make(42.0)->isInt())->toBeTrue();
});

test('isInt() returns false for floats with fractional part', function () {
    expect(Numberable::make(3.14)->isInt())->toBeFalse();
});

test('isFloat() returns true for non-integer floats', function () {
    expect(Numberable::make(3.14)->isFloat())->toBeTrue();
});

test('isFloat() returns false for whole numbers', function () {
    expect(Numberable::make(42)->isFloat())->toBeFalse();
});

test('isPositive() returns true for positive values', function () {
    expect(Numberable::make(1)->isPositive())->toBeTrue();
});

test('isPositive() returns false for zero', function () {
    expect(Numberable::make(0)->isPositive())->toBeFalse();
});

test('isPositive() returns false for negative values', function () {
    expect(Numberable::make(-1)->isPositive())->toBeFalse();
});

test('isNegative() returns true for negative values', function () {
    expect(Numberable::make(-1)->isNegative())->toBeTrue();
});

test('isNegative() returns false for zero', function () {
    expect(Numberable::make(0)->isNegative())->toBeFalse();
});

test('isNegative() returns false for positive values', function () {
    expect(Numberable::make(1)->isNegative())->toBeFalse();
});

test('isZero() returns true for integer zero', function () {
    expect(Numberable::make(0)->isZero())->toBeTrue();
});

test('isZero() returns true for float zero', function () {
    expect(Numberable::make(0.0)->isZero())->toBeTrue();
});

test('isZero() returns false for non-zero values', function () {
    expect(Numberable::make(1)->isZero())->toBeFalse();
});


test('value() returns the raw value', function () {
    expect(Numberable::make(42)->value())->toBe(42);
});

test('toInt() casts to integer', function () {
    $result = Numberable::make(3.9)->toInt();

    expect($result)->toBe(3)
        ->and($result)->toBeInt();
});

test('toFloat() casts to float', function () {
    $result = Numberable::make(42)->toFloat();

    expect($result)->toBe(42.0)
        ->and($result)->toBeFloat();
});


test('pairs() generates correct range pairs', function () {
    $pairs = Numberable::make(0)->pairs(10, 3);

    expect($pairs)->toBe([
        [0, 10],
        [10, 20],
        [20, 30],
    ]);
});

test('pairs() works with float steps', function () {
    $pairs = Numberable::make(0.0)->pairs(0.5, 2);

    expect($pairs)->toBe([
        [0.0, 0.5],
        [0.5, 1.0],
    ]);
});

test('pairs() starts from the current value', function () {
    $pairs = Numberable::make(100)->pairs(25, 2);

    expect($pairs)->toBe([
        [100, 125],
        [125, 150],
    ]);
});

test('pairs() returns empty array for count zero', function () {
    $pairs = Numberable::make(0)->pairs(10, 0);

    expect($pairs)->toBeEmpty();
});


test('format() returns a formatted string', function () {
    $result = Numberable::make(1234567)->format();

    expect($result)->toBeString()
        ->and($result)->toContain('1');
});

test('format() with precision', function () {
    $result = Numberable::make(3.14159)->withPrecision(2)->format();

    expect($result)->toMatch('/3[.,]14/');
});

test('format() with maxPrecision', function () {
    $result = Numberable::make(3.10)->withMaxPrecision(2)->format();

    expect($result)->toBeString();
});

test('format() with locale', function () {
    $result = Numberable::make(1234.56)->withLocale('fr')->format();

    expect($result)->toBeString();
});


test('asPercentage() formats as percentage', function () {
    $result = Numberable::make(75)->asPercentage()->format();

    expect($result)->toContain('75')
        ->and($result)->toContain('%');
});

test('asCurrency() formats as currency with default USD', function () {
    $result = Numberable::make(1000)->asCurrency()->format();

    expect($result)->toBeString()
        ->and($result)->toContain('1');
});

test('asCurrency() accepts a specific currency', function () {
    $result = Numberable::make(1000)->asCurrency('EUR')->format();

    expect($result)->toBeString();
});

test('asCurrency() uses withCurrency()', function () {
    $result = Numberable::make(1000)->withCurrency('GBP')->asCurrency()->format();

    expect($result)->toBeString();
});

test('asOrdinal() formats as ordinal', function () {
    $result = Numberable::make(1)->asOrdinal()->format();

    expect($result)->toBeString();
});

test('asSpell() spells out the number', function () {
    $result = Numberable::make(5)->asSpell()->format();

    expect($result)->toBeString()
        ->and(strtolower($result))->toContain('five');
});

test('asFileSize() formats bytes as human readable size', function () {
    $result = Numberable::make(1024)->asFileSize()->format();

    expect($result)->toBeString()
        ->and($result)->toContain('KB');
});

test('asFileSize() formats megabytes', function () {
    $result = Numberable::make(1048576)->asFileSize()->format();

    expect($result)->toBeString()
        ->and($result)->toContain('MB');
});

test('asAbbreviated() abbreviates large numbers', function () {
    $result = Numberable::make(1000)->asAbbreviated()->format();

    expect($result)->toBeString()
        ->and($result)->toContain('K');
});

test('asAbbreviated() abbreviates millions', function () {
    $result = Numberable::make(1000000)->asAbbreviated()->format();

    expect($result)->toBeString()
        ->and($result)->toContain('M');
});


test('formatAs() with currency style', function () {
    $result = Numberable::make(50)->formatAs('currency', ['currency' => 'USD']);

    expect($result)->toBeString();
});

test('formatAs() with percentage style', function () {
    $result = Numberable::make(0.85)->formatAs('percentage');

    expect($result)->toContain('%');
});

test('formatAs() with spell style', function () {
    $result = Numberable::make(3)->formatAs('spell');

    expect(strtolower($result))->toContain('three');
});

test('formatAs() with ordinal style', function () {
    $result = Numberable::make(2)->formatAs('ordinal');

    expect($result)->toBeString();
});

test('formatAs() with abbreviated style', function () {
    $result = Numberable::make(2500)->formatAs('abbreviated');

    expect($result)->toBeString();
});

test('formatAs() with summarized style (alias for abbreviated)', function () {
    $result = Numberable::make(2500)->formatAs('summarized');

    expect($result)->toBeString();
});

test('formatAs() with fileSize style', function () {
    $result = Numberable::make(2048)->formatAs('fileSize');

    expect($result)->toBeString();
});

test('formatAs() with humanReadable style (alias for fileSize)', function () {
    $result = Numberable::make(2048)->formatAs('humanReadable');

    expect($result)->toBeString();
});

test('formatAs() with unknown style throws InvalidArgumentException', function () {
    Numberable::make(42)->formatAs('unknown_style');
})->throws(\InvalidArgumentException::class, 'Unknown format style: [unknown_style]');


test('registerFormat() adds a custom format', function () {
    Numberable::registerFormat('double', fn ($value) => $value * 2);

    $result = Numberable::make(21)->formatAs('double');

    expect($result)->toBe('42');

    Numberable::flushFormats();
});

test('custom format receives options', function () {
    Numberable::registerFormat('prefixed', fn ($value, $options) => ($options['prefix'] ?? '') . $value);

    $result = Numberable::make(42)->formatAs('prefixed', ['prefix' => '#']);

    expect($result)->toBe('#42');

    Numberable::flushFormats();
});

test('custom format overrides built-in styles', function () {
    Numberable::registerFormat('currency', fn ($value) => "CUSTOM:{$value}");

    $result = Numberable::make(100)->formatAs('currency');

    expect($result)->toBe('CUSTOM:100');

    Numberable::flushFormats();
});

test('flushFormats() removes all custom formats', function () {
    Numberable::registerFormat('test', fn ($value) => 'test');

    Numberable::flushFormats();

    Numberable::make(1)->formatAs('test');
})->throws(\InvalidArgumentException::class);


test('Numberable implements Stringable', function () {
    $number = Numberable::make(42);

    expect($number)->toBeInstanceOf(\Stringable::class);
});

test('__toString returns string representation of integer', function () {
    $result = (string) Numberable::make(42);

    expect($result)->toBe('42');
});

test('__toString returns string representation of float', function () {
    $result = (string) Numberable::make(3.14);

    expect($result)->toBe('3.14');
});

test('__toString with precision formats the number', function () {
    $result = (string) Numberable::make(3.14159)->withPrecision(2);

    expect($result)->toBe('3.14');
});

test('__toString with formatStyle uses format()', function () {
    $result = (string) Numberable::make(75)->asPercentage();

    expect($result)->toContain('%');
});

test('casting to string in interpolation works', function () {
    $number = Numberable::make(42);
    $message = "The answer is {$number}";

    expect($message)->toBe('The answer is 42');
});


test('very large numbers are handled', function () {
    $result = Numberable::make(PHP_INT_MAX)->value();

    expect($result)->toBe(PHP_INT_MAX);
});

test('very small floats are handled', function () {
    $result = Numberable::make(PHP_FLOAT_EPSILON)->value();

    expect($result)->toBe(PHP_FLOAT_EPSILON);
});

test('negative zero is treated as zero', function () {
    expect(Numberable::make(-0)->isZero())->toBeTrue();
});

test('chaining with formatting produces correct output', function () {
    $result = Numberable::make(100)
        ->add(50)
        ->multiply(2)
        ->asPercentage()
        ->format();

    expect($result)->toContain('300')
        ->and($result)->toContain('%');
});


test('can create via make()', function () {
    $n = Numberable::make(42);
    expect($n->value())->toBe(42);
});

test('can parse string fr_FR', function () {
    $n = Numberable::parse('1 234,56', 'fr_FR');
    expect($n->toFloat())->toBeFloat();
});


test('add', function () {
    expect(Numberable::make(10)->add(5)->value())->toBe(15);
});

test('subtract', function () {
    expect(Numberable::make(10)->subtract(3)->value())->toBe(7);
});

test('multiply', function () {
    expect(Numberable::make(4)->multiply(3)->value())->toBe(12);
});

test('divide', function () {
    expect(Numberable::make(10)->divide(2)->value())->toEqual(5);
});

test('divide by zero throws', function () {
    Numberable::make(10)->divide(0);
})->throws(\DivisionByZeroError::class);

test('pow', function () {
    expect(Numberable::make(2)->pow(10)->value())->toEqual(1024);
});

test('abs of negative', function () {
    expect(Numberable::make(-5)->abs()->value())->toEqual(5);
});

test('round', function () {
    expect(Numberable::make(3.14159)->round(2)->value())->toBe(3.14);
});

test('floor', function () {
    expect(Numberable::make(3.9)->floor()->value())->toBe(3.0);
});

test('ceil', function () {
    expect(Numberable::make(3.1)->ceil()->value())->toBe(4.0);
});


test('fluent chain', function () {
    $result = Numberable::make(42)->add(8)->multiply(2)->round(1);
    expect($result->value())->toBe(100.0);
});


test('isInt', function () {
    expect(Numberable::make(5)->isInt())->toBeTrue();
    expect(Numberable::make(5.5)->isInt())->toBeFalse();
});

test('isPositive / isNegative', function () {
    expect(Numberable::make(5)->isPositive())->toBeTrue();
    expect(Numberable::make(-5)->isNegative())->toBeTrue();
});


test('custom format registry', function () {
    Numberable::registerFormat('compact', fn($v) => 'v:' . (int)$v);
    $result = Numberable::make(512)->formatAs('compact');
    expect($result)->toBe('v:512');
    Numberable::flushFormats();
});


test('asPercentage wither + divide', function () {
    // 0.5 as percentage / 2 → 25%
    $result = Numberable::make(0.5)
        ->asPercentage()
        ->multiply(100)
        ->divide(2)
        ->formatAs('percentage');
    expect($result)->toContain('25');
});


test('number() helper returns Numberable', function () {
    $n = number(42);
    expect($n)->toBeInstanceOf(Numberable::class);
    expect($n->add(8)->value())->toBe(50);
});

test('number() helper returns null for null', function () {
    expect(number(null))->toBeNull();
});