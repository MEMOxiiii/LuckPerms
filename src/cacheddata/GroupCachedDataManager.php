<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\cacheddata;

/**
 * Cached data manager specifically for {@link Group} objects.
 */
class GroupCachedDataManager extends HolderCachedDataManager{
	// inherits invalidate() + performCacheCleanup() + permission/meta cache
}
