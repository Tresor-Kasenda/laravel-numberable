<?php

namespace TresorKasenda\Numberable;

use Illuminate\Support\Number;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Stringable;

/**
 * @phpstan-consistent-constructor
 */
class Numberable implements Stringable
{
    use Conditionable;
    use Dumpable;
    use Macroable;
    use Tappable;

    protected int|float $value;

    protected ?string $locale = null;

    protected ?string $currency = null;

    protected ?int $precision = null;

    protected ?int $maxPrecision = null;

    protected ?string $formatStyle = null;

    /** @var array<string, mixed> */
    protected array $formatOptions = [];

    /** @var array<string, callable> */
    protected static array $customFormats = [];


    public function __construct(int|float $value)
    {
        $this->value = $value;
    }

    public static function make(int|float $value): static
    {
        return new static($value);
    }

    public static function of(int|float $value): static
    {
        return static::make($value);
    }

    public static function from(int|float|string $value, ?string $locale = null): static
    {
        return static::parse($value, $locale);
    }

    public static function parse(int|float|string $value, ?string $locale = null): static
    {
        if (is_string($value)) {
            if ($locale !== null && class_exists(\NumberFormatter::class)) {
                $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
                $parsed = $formatter->parse($value);
                $value = $parsed !== false ? $parsed : (float) str_replace(',', '.', $value);
            } else {
                $value = (float) str_replace(',', '.', $value);
            }
        }

        return new static($value);
    }

    public static function parseInt(int|string $value): static
    {
        return new static((int) $value);
    }

    public static function parseFloat(float|string $value): static
    {
        return new static((float) $value);
    }


    public function withLocale(string $locale): static
    {
        $clone = clone $this;
        $clone->locale = $locale;

        return $clone;
    }

    public function withCurrency(string $currency): static
    {
        $clone = clone $this;
        $clone->currency = $currency;

        return $clone;
    }

    public function withPrecision(int $precision): static
    {
        $clone = clone $this;
        $clone->precision = $precision;

        return $clone;
    }

    public function withMaxPrecision(int $maxPrecision): static
    {
        $clone = clone $this;
        $clone->maxPrecision = $maxPrecision;

        return $clone;
    }


    /**
     * @param  array<string, mixed>  $options
     */
    public function asPercentage(array $options = []): static
    {
        $clone = clone $this;
        $clone->formatStyle = 'percentage';
        $clone->formatOptions = $options;

        return $clone;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function asCurrency(?string $currency = null, array $options = []): static
    {
        $clone = clone $this;
        $clone->formatStyle = 'currency';
        if ($currency) {
            $clone->currency = $currency;
        }
        $clone->formatOptions = $options;

        return $clone;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function asOrdinal(array $options = []): static
    {
        $clone = clone $this;
        $clone->formatStyle = 'ordinal';
        $clone->formatOptions = $options;

        return $clone;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function asSpell(array $options = []): static
    {
        $clone = clone $this;
        $clone->formatStyle = 'spell';
        $clone->formatOptions = $options;

        return $clone;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function asFileSize(array $options = []): static
    {
        $clone = clone $this;
        $clone->formatStyle = 'fileSize';
        $clone->formatOptions = $options;

        return $clone;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function asAbbreviated(array $options = []): static
    {
        $clone = clone $this;
        $clone->formatStyle = 'abbreviated';
        $clone->formatOptions = $options;

        return $clone;
    }


    public function add(int|float $value): static
    {
        $clone = clone $this;
        $clone->value += $value;

        return $clone;
    }

    public function subtract(int|float $value): static
    {
        $clone = clone $this;
        $clone->value -= $value;

        return $clone;
    }

    public function multiply(int|float $value): static
    {
        $clone = clone $this;
        $clone->value *= $value;

        return $clone;
    }

    public function divide(int|float $value): static
    {
        if ($value === 0 || $value === 0.0) {
            throw new \DivisionByZeroError('Division by zero.');
        }

        $clone = clone $this;
        $clone->value /= $value;

        return $clone;
    }

    public function mod(int|float $value): static
    {
        $clone = clone $this;
        $clone->value = fmod($this->value, $value);

        return $clone;
    }

    public function pow(int|float $exponent): static
    {
        $clone = clone $this;
        $clone->value = $this->value ** $exponent;

        return $clone;
    }

    public function abs(): static
    {
        $clone = clone $this;
        $clone->value = abs($this->value);

        return $clone;
    }

    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): static
    {
        $clone = clone $this;
        $clone->value = round($this->value, $precision, $mode); // @phpstan-ignore argument.type

        return $clone;
    }

    public function floor(): static
    {
        $clone = clone $this;
        $clone->value = floor($this->value);

        return $clone;
    }

    public function ceil(): static
    {
        $clone = clone $this;
        $clone->value = ceil($this->value);

        return $clone;
    }

    public function clamp(int|float $min, int|float $max): static
    {
        $clone = clone $this;
        $clone->value = Number::clamp($this->value, $min, $max);

        return $clone;
    }

    public function trim(): static
    {
        $clone = clone $this;
        $clone->value = Number::trim($this->value);

        return $clone;
    }


    public function isInt(): bool
    {
        return is_int($this->value) || fmod($this->value, 1) === 0.0;
    }

    public function isFloat(): bool
    {
        return ! $this->isInt();
    }

    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    public function isNegative(): bool
    {
        return $this->value < 0;
    }

    public function isZero(): bool
    {
        return $this->value === 0 || $this->value === 0.0;
    }

    public function isEven(): bool
    {
        return $this->isInt() && ((int) $this->value % 2 === 0);
    }

    public function isOdd(): bool
    {
        return $this->isInt() && ((int) $this->value % 2 !== 0);
    }

    public function isMultipleOf(int|float $value, float $epsilon = 1e-12): bool
    {
        if ($value === 0 || $value === 0.0) {
            throw new \DivisionByZeroError('Cannot determine multiple of zero.');
        }

        if ($this->isInt() && (is_int($value) || fmod($value, 1) === 0.0)) {
            return ((int) $this->value % (int) $value) === 0;
        }

        $remainder = fmod((float) $this->value, (float) $value);
        $divisor = abs((float) $value);

        return abs($remainder) <= $epsilon
            || abs($remainder - $divisor) <= $epsilon
            || abs($remainder + $divisor) <= $epsilon;
    }

    public function isPrime(): bool
    {
        if (! $this->isInt()) {
            return false;
        }

        if (abs((float) $this->value) > PHP_INT_MAX) {
            return false;
        }

        $number = (int) $this->value;

        if ($number <= 1) {
            return false;
        }

        if ($number <= 3) {
            return true;
        }

        if ($number % 2 === 0) {
            return false;
        }

        $limit = (int) sqrt($number);

        for ($divisor = 3; $divisor <= $limit; $divisor += 2) {
            if ($number % $divisor === 0) {
                return false;
            }
        }

        return true;
    }

    public function equals(int|float $value, float $epsilon = 0.0): bool
    {
        return $epsilon > 0
            ? abs($this->value - $value) <= $epsilon
            : $this->value == $value;
    }

    public function greaterThan(int|float $value): bool
    {
        return $this->value > $value;
    }

    public function greaterThanOrEqualTo(int|float $value): bool
    {
        return $this->value >= $value;
    }

    public function lessThan(int|float $value): bool
    {
        return $this->value < $value;
    }

    public function lessThanOrEqualTo(int|float $value): bool
    {
        return $this->value <= $value;
    }

    public function between(int|float $min, int|float $max, bool $inclusive = true): bool
    {
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        return $inclusive
            ? $this->value >= $min && $this->value <= $max
            : $this->value > $min && $this->value < $max;
    }


    /**
     * Generate pairs [value, value+step] like ranges.
     *
     * @return array<int, array{0: int|float, 1: int|float}>
     */
    public function pairs(int|float $step, int $count): array
    {
        $result = [];
        $current = $this->value;

        for ($i = 0; $i < $count; $i++) {
            $result[] = [$current, $current + $step];
            $current += $step;
        }

        return $result;
    }


    public function format(?int $precision = null, ?int $maxPrecision = null, ?string $locale = null): string
    {
        $precision ??= $this->precision;
        $maxPrecision ??= $this->maxPrecision;
        $locale ??= $this->locale;

        if ($this->formatStyle !== null) {
            return $this->formatAs($this->formatStyle, array_merge($this->formatOptions, array_filter([
                'precision' => $precision,
                'locale' => $locale,
            ])));
        }

        return (string) Number::format($this->value, $precision, $maxPrecision, $locale);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function formatAs(string $style, array $options = []): string
    {
        /** @var ?string $locale */
        $locale = $options['locale'] ?? $this->locale;
        /** @var ?int $precision */
        $precision = $options['precision'] ?? $this->precision;
        /** @var ?string $currency */
        $currency = $options['currency'] ?? $this->currency;

        if (isset(static::$customFormats[$style])) {
            /** @var callable(int|float, array<string, mixed>): (string|int|float) $callback */
            $callback = static::$customFormats[$style];

            return (string) $callback($this->value, $options);
        }

        return (string) match ($style) {
            'currency'      => Number::currency($this->value, $currency ?? 'USD', $locale),
            'percentage'    => Number::percentage($this->value, $precision ?? 0, null, $locale),
            'spell'         => Number::spell($this->value, $locale),
            'ordinal'       => Number::ordinal($this->value, $locale),
            'spellOrdinal'  => Number::spellOrdinal($this->value, $locale),
            'abbreviated',
            'summarized'    => Number::abbreviate($this->value, $precision ?? 0),
            'fileSize',
            'humanReadable' => Number::fileSize($this->value, $precision ?? 0),
            default         => throw new \InvalidArgumentException("Unknown format style: [{$style}]"),
        };
    }


    public static function registerFormat(string $name, callable $formatter): void
    {
        static::$customFormats[$name] = $formatter;
    }

    public static function flushFormats(): void
    {
        static::$customFormats = [];
    }

    public function value(): int|float
    {
        return $this->value;
    }

    public function toInt(): int
    {
        return (int) $this->value;
    }

    public function toFloat(): float
    {
        return (float) $this->value;
    }

    public function __toString(): string
    {
        if ($this->formatStyle !== null) {
            return $this->format();
        }

        return $this->precision !== null
            ? number_format($this->value, $this->precision)
            : (string) $this->value;
    }
}
