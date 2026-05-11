<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

/**
 * Factory for creating {@link Constraint} instances.
 */
class ConstraintFactory{
	/**
	 * Create a string constraint using the given comparison and value.
	 *
	 * @param Comparison $comparison
	 * @param string $value
	 * @return Constraint<string>
	 */
	public static function forString(Comparison $comparison, string $value) : Constraint{
		return Constraint::forString($comparison, $value);
	}

	/**
	 * Parse a constraint from comparison symbol + value.
	 *
	 * @param string $comparisonSymbol e.g. "==", "!=", "~~", "!~"
	 * @param string $value
	 * @return Constraint<string>|null null if the symbol is not recognized
	 */
	public static function parse(string $comparisonSymbol, string $value) : ?Constraint{
		$comparison = Comparison::parse($comparisonSymbol);
		if($comparison === null){
			return null;
		}
		return self::forString($comparison, $value);
	}
}
