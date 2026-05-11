<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\cacheddata;

use jasonw4331\LuckPerms\api\query\QueryOptions;

/**
 * Manages a specific type of CachedData within a CachedDataManager instance.
 */
interface CachedDataContainer
{
	public function get(QueryOptions $queryOptions) : CachedData;

	public function calculate(QueryOptions $queryOptions) : CachedData;

	public function invalidate(QueryOptions $queryOptions) : void;

	public function invalidateAll() : void;

	public function recalculate(QueryOptions $queryOptions) : void;

	public function recalculateAll() : void;
}
