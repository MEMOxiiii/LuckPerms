<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\cacheddata;

use function array_key_exists;
use function count;

/**
 * Cached data manager for a generic permission holder.
 * Extends {@link AbstractCachedDataManager} with common holder-specific logic.
 */
abstract class HolderCachedDataManager extends AbstractCachedDataManager{
	/** @var array<string, mixed> simple key-value cache for permission lookups */
	protected array $permissionCache = [];
	/** @var array<string, mixed> simple key-value cache for meta lookups */
	protected array $metaCache = [];

	public function invalidate() : void{
		$this->permissionCache = [];
		$this->metaCache = [];
	}

	public function performCacheCleanup() : void{
		// Basic LRU: if cache exceeds 1000 entries, clear it
		if(count($this->permissionCache) > 1000){
			$this->permissionCache = [];
		}
		if(count($this->metaCache) > 1000){
			$this->metaCache = [];
		}
	}

	/**
	 * Cache a permission lookup result.
	 */
	public function cachePermission(string $permission, ?bool $result) : void{
		$this->permissionCache[$permission] = $result;
	}

	/**
	 * Look up a cached permission result.
	 *
	 * @return bool|null null means not cached
	 */
	public function getCachedPermission(string $permission) : ?bool{
		return $this->permissionCache[$permission] ?? null;
	}

	/**
	 * Cache a meta value.
	 */
	public function cacheMeta(string $key, ?string $value) : void{
		$this->metaCache[$key] = $value;
	}

	/**
	 * Look up a cached meta value.
	 * Returns false if not cached; null if cached as absent.
	 *
	 * @return string|null|false false = not in cache
	 */
	public function getCachedMeta(string $key) : string|null|false{
		if(!array_key_exists($key, $this->metaCache)){
			return false;
		}
		return $this->metaCache[$key];
	}
}
