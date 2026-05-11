<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context\manager;

use jasonw4331\LuckPerms\query\QueryOptions;

/**
 * A {@link QueryOptionsSupplier} backed by an inline (lambda/callable) provider.
 */
class InlineQueryOptionsSupplier extends QueryOptionsSupplier{
	/** @var callable(): QueryOptions */
	private $supplier;

	/** @param callable(): QueryOptions $supplier */
	public function __construct(callable $supplier){
		$this->supplier = $supplier;
	}

	public function getQueryOptions() : QueryOptions{
		return ($this->supplier)();
	}
}
