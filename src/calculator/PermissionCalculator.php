<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\calculator;

use jasonw4331\LuckPerms\calculator\processor\PermissionProcessor;
use jasonw4331\LuckPerms\calculator\result\TristateResult;
use function strtolower;

/**
 * Calculates and caches permissions using a processor chain
 */
class PermissionCalculator {

	/** @var PermissionProcessor[] */
	private array $processors;

	/** @var array<string, TristateResult> Loading cache for permission checks */
	private array $cache = [];

	/**
	 * @param PermissionProcessor[] $processors
	 */
	public function __construct(array $processors) {
		$this->processors = $processors;
	}

	/**
	 * Performs a permission check against this calculator.
	 *
	 * @param permission the permission to check
	 * @return the result
	 */
	public function checkPermission(string $permission) : TristateResult {
		$permissionLower = strtolower($permission);
		
		// Check cache first
		if(isset($this->cache[$permissionLower])) {
			return $this->cache[$permissionLower];
		}

		// Process through all processors
		$result = TristateResult::UNDEFINED;
		foreach($this->processors as $processor) {
			$result = $processor->hasPermission($result, $permissionLower);
			// If a processor returned a defined result, we can stop
			if($result->isDefined()) {
				break;
			}
		}

		// Cache the result
		$this->cache[$permissionLower] = $result;
		return $result;
	}

	public function invalidateCache() : void {
		$this->cache = [];
		foreach($this->processors as $processor) {
			$processor->invalidate();
		}
	}
}
