<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\util;

/**
 * Represents a three-state boolean: TRUE, FALSE, or UNDEFINED.
 */
enum Tristate : string
{
	case TRUE = 'TRUE';
	case FALSE = 'FALSE';
	case UNDEFINED = 'UNDEFINED';

	public function asBoolean() : ?bool
	{
		return match ($this) {
			self::TRUE => true,
			self::FALSE => false,
			self::UNDEFINED => null,
		};
	}

	public static function fromBoolean(bool $value) : self
	{
		return $value ? self::TRUE : self::FALSE;
	}

	public static function fromNullableBoolean(?bool $value) : self
	{
		if ($value === null) return self::UNDEFINED;
		return $value ? self::TRUE : self::FALSE;
	}
}
