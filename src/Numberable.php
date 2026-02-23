<?php

namespace TresorKasenda\Numberable;

use Illuminate\Support\Number;
use Illuminate\Support\Traits\Macroable;
use Stringable;

/**
 * @phpstan-consistent-constructor
 */
class Numberable implements Stringable
{
    use Macroable;

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