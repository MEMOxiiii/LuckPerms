<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

use function preg_quote;
use function str_replace;

/**
 * A method of comparing two strings (maps to SQL comparison operators).
 */
enum Comparison : string{
	/**
	 * Exact equality.
	 */
	case EQUAL = '==';
	/**
	 * Not equal.
	 */
	case NOT_EQUAL = '!=';
	/**
	 * SQL LIKE pattern (% and _ wildcards).
	 */
	case SIMILAR = '~~';
	/**
	 * SQL NOT LIKE pattern.
	 */
	case NOT_SIMILAR = '!~';

	public const WILDCARD = '%';
	public const WILDCARD_ONE = '_';

	public function getSymbol() : string{
		return $this->value;
	}

	public static function parse(string $s) : ?self{
		foreach(self::cases() as $case){
			if($case->value === $s){
				return $case;
			}
		}
		return null;
	}

	/**
	 * Compile a SQL LIKE pattern to a PHP regex.
	 */
	public static function compilePatternForLikeSyntax(string $expression) : string{
		$expression = preg_quote($expression, '/');
		// convert from SQL LIKE syntax to regex
		$expression = str_replace(preg_quote(self::WILDCARD_ONE, '/'), '.', $expression);
		$expression = str_replace(preg_quote(self::WILDCARD, '/'), '.*', $expression);
		return '/^' . $expression . '$/i';
	}
}
