<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

use function preg_match;

/**
 * A constraint that evaluates a value against a {@link Comparison} and a target value.
 *
 * @template T
 */
class Constraint{
	/** @var callable(T): bool */
	private $predicate;

	/**
	 * @param T                 $value
	 * @param callable(T): bool $predicate
	 */
	public function __construct(
		private Comparison $comparison,
		private mixed $value,
		callable $predicate
	){
		$this->predicate = $predicate;
	}

	public function comparison() : Comparison{
		return $this->comparison;
	}

	/** @return T */
	public function value() : mixed{
		return $this->value;
	}

	/**
	 * @param T $value
	 */
	public function evaluate(mixed $value) : bool{
		return ($this->predicate)($value);
	}

	public function __toString() : string{
		return $this->comparison->getSymbol() . ' ' . $this->value;
	}

	// ---- Factory methods for string constraints ----

	public static function forString(Comparison $comparison, string $value) : self{
		return match ($comparison) {
			Comparison::EQUAL => new self($comparison, $value, static fn($v) => (string) $v === $value),
			Comparison::NOT_EQUAL => new self($comparison, $value, static fn($v) => (string) $v !== $value),
			Comparison::SIMILAR => (static function() use ($comparison, $value) : self{
				$pattern = Comparison::compilePatternForLikeSyntax($value);
				return new self($comparison, $value, static fn($v) => (bool) preg_match($pattern, (string) $v));
			})(),
			Comparison::NOT_SIMILAR => (static function() use ($comparison, $value) : self{
				$pattern = Comparison::compilePatternForLikeSyntax($value);
				return new self($comparison, $value, static fn($v) => !preg_match($pattern, (string) $v));
			})(),
		};
	}
}
