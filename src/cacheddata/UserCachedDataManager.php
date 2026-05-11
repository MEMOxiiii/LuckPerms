<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\cacheddata;

/**
 * Cached data manager specifically for {@link User} objects.
 */
class UserCachedDataManager extends HolderCachedDataManager{
	// inherits invalidate() + performCacheCleanup() + permission/meta cache
}

