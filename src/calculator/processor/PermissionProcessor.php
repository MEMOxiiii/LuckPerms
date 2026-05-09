<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\calculator\processor;

use jasonw4331\LuckPerms\calculator\result\TristateResult;

/**
 * A processor within a PermissionCalculator.
 *
 * Processors should not implement any sort of caching. This is handled in the parent calculator.
 */
interface PermissionProcessor {

	/**
	 * Returns the permission value determined by this processor.
	 *
	 * @param prev the result of the previous processor in the chain
	 * @param permission the permission
	 * @return a tristate
	 */
	public function hasPermission(TristateResult $prev, string $permission) : TristateResult;

	/**
	 * Called after the parent calculator has been invalidated
	 */
	public function invalidate() : void;
}
