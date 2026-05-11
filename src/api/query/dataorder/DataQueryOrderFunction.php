<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query\dataorder;

use jasonw4331\LuckPerms\api\model\PermissionHolder;
use jasonw4331\LuckPerms\api\query\OptionKey;
use jasonw4331\LuckPerms\api\query\SimpleOptionKey;

/**
 * A function that generates a DataQueryOrder comparator for PermissionHolders.
 */
interface DataQueryOrderFunction
{
    /**
     * The OptionKey for DataQueryOrderFunction.
     */
    public static function key(): OptionKey;

    /**
     * Gets the DataQueryOrder comparator callable for the given holder identifier.
     * The returned callable accepts two DataType values and returns int.
     */
    public function getOrderComparator(PermissionHolder\Identifier $holderIdentifier): callable;
}
