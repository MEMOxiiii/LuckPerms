<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model\nodemap;

use jasonw4331\LuckPerms\node\NodeEntry;

/**
 * Base interface/contract for a node map — a collection of {@link NodeEntry} objects
 * that belong to a permission holder.
 */
abstract class NodeMap{
	/** @return NodeEntry[] all nodes in this map */
	abstract public function asList() : array;

	/** @return NodeEntry[] all non-expired nodes */
	abstract public function asListActive() : array;

	abstract public function add(NodeEntry $node) : void;

	abstract public function remove(NodeEntry $node) : bool;

	abstract public function clear() : void;

	abstract public function setNodes(array $nodes) : void;

	/** Returns true if the map contains no nodes. */
	public function isEmpty() : bool{
		return count($this->asList()) === 0;
	}
}

