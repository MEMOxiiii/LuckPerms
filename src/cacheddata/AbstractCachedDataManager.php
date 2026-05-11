<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\cacheddata;

/**
 * Abstract base for all cached data managers.
 * Manages lifecycle of permission/meta data caches for a holder.
 */
abstract class AbstractCachedDataManager{
	/** Invalidates all cached data for this holder. */
	abstract public function invalidate() : void;

	/** Removes cached data that is no longer actively used. */
	abstract public function performCacheCleanup() : void;
}

