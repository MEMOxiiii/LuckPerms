<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\node\matcher;

use jasonw4331\LuckPerms\api\node\Node;
use jasonw4331\LuckPerms\api\node\NodeEqualityPredicate;
use jasonw4331\LuckPerms\api\node\NodeType;

/**
 * Creates NodeMatcher instances.
 */
interface NodeMatcherFactory
{
	public function key(string $key) : NodeMatcher;

	public function keyFromNode(Node $node) : NodeMatcher;

	public function keyStartsWith(string $startingWith) : NodeMatcher;

	public function equals(Node $other, NodeEqualityPredicate $predicate) : NodeMatcher;

	public function metaKey(string $metaKey) : NodeMatcher;

	public function type(NodeType $type) : NodeMatcher;
}
