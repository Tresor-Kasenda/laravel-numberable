# Laravel Numberable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tresor-kasenda/laravel-numberable.svg?style=flat-square)](https://packagist.org/packages/tresor-kasenda/laravel-numberable)
[![Tests](https://img.shields.io/github/actions/workflow/status/Tresor-Kasenda/laravel-numberable/tests.yml?label=tests&style=flat-square)](https://github.com/Tresor-Kasenda/laravel-numberable/actions)
[![License](https://img.shields.io/packagist/l/tresor-kasenda/laravel-numberable.svg?style=flat-square)](https://packagist.org/packages/tresor-kasenda/laravel-numberable)

A fluent, expressive API for numeric operations in Laravel — **like `Stringable`, but for numbers**.

```php
use TresorKasenda\Numberable\Numberable;

Numberable::make(1500)
    ->add(500)
    ->multiply(1.1)
    ->asCurrency('USD')
    ->withLocale('en_US')
    ->format(); // "$2,200.00"
```

## Requirements

- PHP 8.3+
- Laravel 10, 11, or 12
- `ext-intl` PHP extension

## Installation

```bash
composer require tresor-kasenda/laravel-numberable
```

The package auto-registers its service provider via Laravel's package discovery.

## Quick Start

There are several ways to create a `Numberable` instance:

```php
use TresorKasenda\Numberable\Numberable;

// Using the static factory
$n = Numberable::make(42);

// Using the global helper
$n = number(42);

// Parsing strings (handles comma decimals)
$n = Numberable::parse('1.234,56', 'de_DE');

// Strict type parsing
$n = Numberable::parseInt('42');   // int
$n = Numberable::parseFloat('3.14'); // float
```

> The `number()` helper returns `null` when given `null`, making it safe for nullable values.

## Arithmetic Operations

All operations are **immutable** — they return a new instance, leaving the original untouched.

```php
$price = Numberable::make(100);

$price->add(50);         // 150
$price->subtract(20);    // 80
$price->multiply(2);     // 200
$price->divide(4);       // 25
$price->mod(3);          // 1
$price->pow(2);          // 10000
$price->abs();           // 100 (useful for negatives)
$price->round(2);        // rounds to 2 decimals
$price->floor();         // rounds down
$price->ceil();          // rounds up
```

### Chaining

Chain multiple operations fluently:

```php
$result = number(100)
    ->add(50)
    ->multiply(2)
    ->subtract(10)
    ->divide(3)
    ->round(2)
    ->value(); // 63.33
```

> Division by zero throws a `\DivisionByZeroError`.

## Formatting

### Basic Formatting

```php
number(1234567)->format();                  // "1,234,567"
number(3.14159)->withPrecision(2)->format(); // "3.14"
number(1234.5)->withLocale('fr')->format();  // "1 234,5"
```

### Format Styles

```php
// Currency
number(1000)->asCurrency('EUR')->format();           // "€1,000.00"
number(1000)->withCurrency('USD')->asCurrency()->format(); // "$1,000.00"

// Percentage
number(75)->asPercentage()->format();   // "75%"

// Ordinal
number(1)->asOrdinal()->format();       // "1st"

// Spell out
number(5)->asSpell()->format();         // "five"

// File size
number(1048576)->asFileSize()->format(); // "1 MB"

// Abbreviation
number(1000000)->asAbbreviated()->format(); // "1M"
```

### Direct Style Formatting

Use `formatAs()` to format with a specific style directly:

```php
number(42)->formatAs('spell');       // "forty-two"
number(2)->formatAs('ordinal');      // "2nd"
number(1500)->formatAs('currency', ['currency' => 'GBP']); // "£1,500.00"
number(2048)->formatAs('fileSize');   // "2 KB"
```

**Available styles:** `currency`, `percentage`, `spell`, `ordinal`, `spellOrdinal`, `abbreviated` (alias: `summarized`), `fileSize` (alias: `humanReadable`)

### Locale Support

Apply locale for any formatting via `withLocale()`:

```php
number(1234.56)->withLocale('de_DE')->format(); // "1.234,56"
number(1000)->withLocale('fr_FR')->asCurrency('EUR')->format(); // "1 000,00 €"
```

## Fluent Configuration

Configure formatting options via immutable "with" methods:

```php
number(1234.5678)
    ->withLocale('en_US')
    ->withPrecision(2)
    ->format(); // "1,234.57"

number(99.99)
    ->withCurrency('EUR')
    ->asCurrency()
    ->format(); // "€99.99"

number(3.14159)
    ->withMaxPrecision(3)
    ->format(); // "3.142"
```

## Type Checks

```php
number(42)->isInt();       // true
number(3.14)->isInt();     // false
number(3.14)->isFloat();   // true
number(5)->isPositive();   // true
number(-5)->isNegative();  // true
number(0)->isZero();       // true
```

## Value Accessors

```php
number(3.9)->value();   // 3.9 (raw int|float)
number(3.9)->toInt();   // 3
number(42)->toFloat();  // 42.0
```

## Pairs

Generate range pairs — useful for building histograms, sliders, or pagination ranges:

```php
number(0)->pairs(10, 3);
// [[0, 10], [10, 20], [20, 30]]

number(100)->pairs(25, 4);
// [[100, 125], [125, 150], [150, 175], [175, 200]]
```

## Stringable

`Numberable` implements `Stringable`, so it works anywhere a string is expected:

```php
echo number(42);                    // "42"
echo number(3.14)->withPrecision(1); // "3.1"
echo number(75)->asPercentage();     // "75%"

$message = "Total: " . number(1000)->asCurrency('USD');
// "Total: $1,000.00"
```

## Custom Formats

Register your own named format styles:

```php
Numberable::registerFormat('compact', function (int|float $value, array $options = []) {
    $prefix = $options['prefix'] ?? '';
    return $prefix . number_format($value, 0, '.', 'k');
});

number(512)->formatAs('compact');                    // "512"
number(512)->formatAs('compact', ['prefix' => '#']); // "#512"
```

Custom formats take priority over built-in styles, allowing you to override defaults:

```php
Numberable::registerFormat('currency', fn ($value) => "CUSTOM: $value");
number(100)->formatAs('currency'); // "CUSTOM: 100"
```

Clear all custom formats with:

```php
Numberable::flushFormats();
```

## Macros

Extend `Numberable` with your own methods using Laravel's `Macroable` trait:

```php
// Register a macro (e.g., in a service provider)
Numberable::macro('double', function () {
    return $this->multiply(2);
});

Numberable::macro('taxed', function (float $rate = 0.2) {
    return $this->multiply(1 + $rate);
});

// Use it
number(50)->double()->value();      // 100
number(100)->taxed(0.15)->value();  // 115.0

// Check if a macro exists
Numberable::hasMacro('double'); // true
```

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
