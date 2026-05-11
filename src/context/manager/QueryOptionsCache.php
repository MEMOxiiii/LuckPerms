<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context\manager;

use jasonw4331\LuckPerms\query\QueryOptions;

/**
 * Caches the resolved {@link QueryOptions} for a subject (e.g. a player).
 * The cache is invalidated whenever the context of the subject changes.
 */
class QueryOptionsCache{
	private ?QueryOptions $cached = null;

	public function get() : ?QueryOptions{
		return $this->cached;
	}

	public function set(QueryOptions $options) : void{
		$this->cached = $options;
	}

	public function invalidate() : void{
		$this->cached = null;
	}
}

