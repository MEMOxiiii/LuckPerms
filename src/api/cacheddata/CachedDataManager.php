<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\cacheddata;

use jasonw4331\LuckPerms\api\query\QueryOptions;

/**
 * Holds cached permission and meta lookup data for a PermissionHolder.
 */
interface CachedDataManager
{
	public function permissionData() : CachedDataContainer;

	public function metaData() : CachedDataContainer;

	public function getPermissionData(QueryOptions $queryOptions) : CachedPermissionData;

	public function getMetaData(QueryOptions $queryOptions) : CachedMetaData;

	public function getPermissionDataDefault() : CachedPermissionData;

	public function getMetaDataDefault() : CachedMetaData;

	public function invalidate() : void;

	public function invalidatePermissionCalculators() : void;
}
