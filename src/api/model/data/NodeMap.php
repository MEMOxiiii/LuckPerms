<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\data;

use jasonw4331\LuckPerms\api\context\ContextSet;
use jasonw4331\LuckPerms\api\node\Node;
use jasonw4331\LuckPerms\api\node\NodeEqualityPredicate;
use jasonw4331\LuckPerms\api\util\Tristate;

/**
 * Encapsulates a store of Nodes within a PermissionHolder.
 */
interface NodeMap
{
	/**
	 * Gets a map of the Nodes contained within this instance, mapped to their context.
	 *
	 * @return array<array<Node>>
	 */
	public function toMap() : array;

	/**
	 * Gets a flattened view of Nodes contained within this instance.
	 *
	 * @return Node[]
	 */
	public function toCollection() : array;

	/**
	 * Gets if this instance contains a given Node.
	 */
	public function contains(Node $node, NodeEqualityPredicate $equalityPredicate) : Tristate;

	/**
	 * Adds a node.
	 */
	public function add(Node $node) : DataMutateResult;

	/**
	 * Adds a node with a TemporaryNodeMergeStrategy.
	 */
	public function addWithMergeStrategy(Node $node, TemporaryNodeMergeStrategy $temporaryNodeMergeStrategy) : DataMutateResultWithMergedNode;

	/**
	 * Removes a node.
	 */
	public function remove(Node $node) : DataMutateResult;

	/**
	 * Clears all nodes.
	 */
	public function clear() : void;

	/**
	 * Clears any nodes which pass the predicate callable.
	 */
	public function clearMatching(callable $test) : void;

	/**
	 * Clears all nodes in a specific context.
	 */
	public function clearContext(ContextSet $contextSet) : void;

	/**
	 * Clears all nodes in a specific context which pass the predicate callable.
	 */
	public function clearContextMatching(ContextSet $contextSet, callable $test) : void;
}
