<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context\manager;

use jasonw4331\LuckPerms\query\QueryOptions;

/**
 * Supplies a {@link QueryOptions} instance (typically for a specific player or context).
 */
abstract class QueryOptionsSupplier{
	abstract public function getQueryOptions() : QueryOptions;
}
