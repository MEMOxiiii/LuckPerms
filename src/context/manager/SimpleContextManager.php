<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context\manager;

use jasonw4331\LuckPerms\query\QueryOptions;
use jasonw4331\LuckPerms\query\QueryOptionsImpl;

/**
 * A simple, non-platform-specific context manager.
 * Used when no subject-specific context is available (e.g. for offline players or API calls).
 */
class SimpleContextManager extends ContextManagerBase{
	public function getQueryOptions(mixed $subject = null) : QueryOptions{
		if($subject === null){
			return new QueryOptionsImpl();
		}
		// Build context-aware query options when a subject is present
		$contextSet = $this->buildContexts($subject);
		return new QueryOptionsImpl($contextSet);
	}
}
