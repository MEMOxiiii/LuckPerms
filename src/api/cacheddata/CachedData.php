<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\cacheddata;

use jasonw4331\LuckPerms\api\query\QueryOptions;

/**
 * Holds cached lookup data for a given set of query options.
 */
interface CachedData
{
	public function getQueryOptions() : QueryOptions;
}
