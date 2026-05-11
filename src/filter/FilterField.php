<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

/**
 * Represents a named field on a value object T that can be extracted for filtering.
 *
 * @template T the type of the value object
 * @template FT the type of the field value
 */
abstract class FilterField{
	/**
	 * Extract the field value from an object of type T.
	 *
	 * @param mixed $value the object
	 * @return mixed the field value
	 */
	abstract public function getValue(mixed $value) : mixed;

	abstract public function __toString() : string;

	/**
	 * Create a filter using this field with the given constraint.
	 */
	public function filter(Constraint $constraint) : Filter{
		return new Filter($this, $constraint);
	}
}
