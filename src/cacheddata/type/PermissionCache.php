<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\cacheddata\type;

use jasonw4331\LuckPerms\cacheddata\result\TristateResult;
use function count;

/**
 * Stores the resolved permission check results for a single query-options context.
 * Maps permission string => TristateResult.
 */
class PermissionCache{
	/** @var array<string, TristateResult> */
	private array $data = [];

	public function get(string $permission) : ?TristateResult{
		return $this->data[$permission] ?? null;
	}

	public function put(string $permission, TristateResult $result) : void{
		$this->data[$permission] = $result;
	}

	public function invalidate() : void{
		$this->data = [];
	}

	public function size() : int{
		return count($this->data);
	}
}
