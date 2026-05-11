<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query\dataorder;

use jasonw4331\LuckPerms\api\model\data\DataType;

/**
 * Represents the order in which to query different DataTypes.
 */
enum DataQueryOrder : string
{
	/**
	 * Query TRANSIENT data first, then NORMAL.
	 */
	case TRANSIENT_FIRST = 'TRANSIENT_FIRST';

	/**
	 * Query NORMAL data first, then TRANSIENT.
	 */
	case TRANSIENT_LAST = 'TRANSIENT_LAST';

	/**
	 * Returns a list of DataTypes ordered according to the given comparator callable.
	 * The callable should accept two DataType values and return a negative, zero, or positive int.
	 *
	 * @return DataType[]
	 */
	public static function order(callable $comparator) : array
	{
		$compare = $comparator(DataType::TRANSIENT, DataType::NORMAL);
		if ($compare > 0) {
			return [DataType::TRANSIENT, DataType::NORMAL];
		} elseif ($compare < 0) {
			return [DataType::NORMAL, DataType::TRANSIENT];
		} else {
			return [DataType::NORMAL, DataType::TRANSIENT];
		}
	}

	/**
	 * Iterates DataTypes in the defined order, calling the consumer for each.
	 */
	public static function queryInOrder(callable $comparator, callable $consumer) : void
	{
		foreach (self::order($comparator) as $dataType) {
			$consumer($dataType);
		}
	}

	/**
	 * Returns a comparator callable for this DataQueryOrder instance.
	 */
	public function comparator() : callable
	{
		return match ($this) {
			self::TRANSIENT_FIRST => function (DataType $o1, DataType $o2) : int {
				if ($o1 === $o2) return 0;
				return $o1 === DataType::TRANSIENT ? 1 : -1;
			},
			self::TRANSIENT_LAST => function (DataType $o1, DataType $o2) : int {
				if ($o1 === $o2) return 0;
				return $o1 === DataType::TRANSIENT ? -1 : 1;
			},
		};
	}
}
