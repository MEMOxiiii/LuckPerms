<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\calculator;

use jasonw4331\LuckPerms\calculator\processor\DirectProcessor;
use jasonw4331\LuckPerms\calculator\processor\WildcardProcessor;
use jasonw4331\LuckPerms\node\NodeEntry;

/**
 * Creates PermissionCalculator instances
 */
class CalculatorFactory {

	/**
	 * Builds a PermissionCalculator with the given source permissions.
	 *
	 * @param array<string, NodeEntry> $sourceMap the source permissions map
	 * @return PermissionCalculator a permission calculator instance
	 */
	public static function build(array $sourceMap) : PermissionCalculator {
		$processors = [
			new DirectProcessor($sourceMap),
			new WildcardProcessor($sourceMap),
		];
		return new PermissionCalculator($processors);
	}
}
