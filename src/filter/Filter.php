<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

/**
 * Represents a single filter on a value of type T.
 *
 * @template T
 * @template FT
 */
class Filter{
	public function __construct(
		private FilterField $field,
		private Constraint $constraint
	){ }

	public function field() : FilterField{
		return $this->field;
	}

	public function constraint() : Constraint{
		return $this->constraint;
	}

	/**
	 * Evaluate the filter against a value.
	 *
	 * @return bool true if the value satisfies this filter
	 */
	public function evaluate(mixed $value) : bool{
		return $this->constraint->evaluate($this->field->getValue($value));
	}

	public function __toString() : string{
		return $this->field . ' ' . $this->constraint;
	}
}
