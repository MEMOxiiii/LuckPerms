<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query;

/**
 * A registry providing useful QueryOptions instances.
 */
interface QueryOptionsRegistry
{
	/**
	 * Gets the default contextual query options.
	 */
	public function defaultContextualOptions() : QueryOptions;

	/**
	 * Gets the default non-contextual query options.
	 */
	public function defaultNonContextualOptions() : QueryOptions;
}
