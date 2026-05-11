<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\node\matcher;

use jasonw4331\LuckPerms\api\node\Node;
use jasonw4331\LuckPerms\api\node\NodeEqualityPredicate;
use jasonw4331\LuckPerms\api\node\NodeType;

/**
 * Matches nodes against a given criteria.
 */
interface NodeMatcher
{
    /**
     * Tests whether the given node matches.
     */
    public function test(Node $node): bool;

    /**
     * Creates a matcher for nodes with a specific key.
     */
    public static function key(string $key): self;

    /**
     * Creates a matcher for nodes equal to a given node's key.
     */
    public static function keyFromNode(Node $node): self;

    /**
     * Creates a matcher for nodes whose key starts with the given string.
     */
    public static function keyStartsWith(string $startingWith): self;

    /**
     * Creates a matcher for nodes equal to the given node using the given predicate.
     */
    public static function equals(Node $other, NodeEqualityPredicate $predicate): self;

    /**
     * Creates a matcher for meta nodes with a specific key.
     */
    public static function metaKey(string $metaKey): self;

    /**
     * Creates a matcher for nodes of a specific type.
     */
    public static function type(NodeType $type): self;
}
