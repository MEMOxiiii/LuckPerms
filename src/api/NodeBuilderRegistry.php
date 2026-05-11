<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api;

use jasonw4331\LuckPerms\api\node\NodeBuilder;
use jasonw4331\LuckPerms\api\node\NodeType;

/**
 * Registry for NodeBuilder instances.
 */
interface NodeBuilderRegistry
{
	/**
	 * Gets a NodeBuilder for the given permission key.
	 */
	public function forKey(string $key) : NodeBuilder;

	/**
	 * Gets a NodeBuilder for the given NodeType.
	 */
	public function forType(NodeType $type) : NodeBuilder;
}
