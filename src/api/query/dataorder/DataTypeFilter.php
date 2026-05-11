<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query\dataorder;

use jasonw4331\LuckPerms\api\model\data\DataType;
use function array_filter;

/**
 * Represents which different DataTypes are used for a query.
 */
enum DataTypeFilter : string
{
	/**
	 * All DataTypes should be used.
	 */
	case ALL = 'ALL';

	/**
	 * No DataTypes should be used.
	 */
	case NONE = 'NONE';

	/**
	 * Only DataType::NORMAL should be used.
	 */
	case NORMAL_ONLY = 'NORMAL_ONLY';

	/**
	 * Only DataType::TRANSIENT should be used.
	 */
	case TRANSIENT_ONLY = 'TRANSIENT_ONLY';

	/**
	 * Tests whether the given DataType passes this filter.
	 */
	public function test(DataType $dataType) : bool
	{
		return match ($this) {
			self::ALL => true,
			self::NONE => false,
			self::NORMAL_ONLY => $dataType === DataType::NORMAL,
			self::TRANSIENT_ONLY => $dataType === DataType::TRANSIENT,
		};
	}

	/**
	 * Returns all DataType values that pass the given predicate callable.
	 *
	 * @return DataType[]
	 */
	public static function values(callable $predicate) : array
	{
		return array_filter(DataType::cases(), $predicate);
	}
}
