<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context;

use jasonw4331\LuckPerms\context\manager\ContextManagerBase;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\query\QueryOptions;
use jasonw4331\LuckPerms\query\QueryOptionsImpl;

/**
 * Platform context manager for PocketMine.
 * Extends {@link ContextManagerBase} and is wired into the plugin lifecycle.
 */
class ContextManager extends ContextManagerBase{
	public function __construct(private LuckPerms $plugin){
		parent::__construct();
	}

	public function getQueryOptions(mixed $subject = null) : QueryOptions{
		if($subject === null){
			return new QueryOptionsImpl();
		}
		$contextSet = $this->buildContexts($subject);
		return new QueryOptionsImpl($contextSet);
	}
}
