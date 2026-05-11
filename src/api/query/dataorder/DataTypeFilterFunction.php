<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query\dataorder;

use jasonw4331\LuckPerms\api\model\PermissionHolder;
use jasonw4331\LuckPerms\api\query\OptionKey;

/**
 * A function that generates a DataTypeFilter predicate for PermissionHolders.
 */
interface DataTypeFilterFunction
{
	/**
	 * The OptionKey for DataTypeFilterFunction.
	 */
	public static function key() : OptionKey;

	/**
	 * Gets the DataTypeFilter predicate callable for the given holder identifier.
	 * The returned callable accepts a DataType and returns bool.
	 */
	public function getTypeFilter(PermissionHolder\Identifier $holderIdentifier) : callable;
}
