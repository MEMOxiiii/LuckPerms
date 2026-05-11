<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model;

use jasonw4331\LuckPerms\inheritance\InheritanceComparator;
use jasonw4331\LuckPerms\model\nodemap\NodeMapMutable;
use jasonw4331\LuckPerms\node\NodeEntry;
use function array_filter;
use function array_values;
use function time;

/**
 * Base class for both {@link User} and {@link Group}.
 * Holds the node map and provides common node-management methods.
 */
abstract class PermissionHolder{
	private NodeMapMutable $data;

	public function __construct(){
		$this->data = new NodeMapMutable();
	}

	// Abstract methods that subclasses must implement
	abstract public function getName() : string;
	abstract public function getWeight() : int;

	/**
	 * Returns the type string for this holder ('user' or 'group').
	 */
	abstract public function getHolderType() : string;

	/** @return NodeEntry[] */
	public function getNodes() : array{
		return $this->data->asList();
	}

	/** @return NodeEntry[] only non-expired nodes */
	public function getActiveNodes() : array{
		return $this->data->asListActive();
	}

	/** @param NodeEntry[] $nodes */
	public function setNodes(array $nodes) : void{
		$this->data->setNodes($nodes);
	}

	public function addNode(NodeEntry $node) : void{
		$this->data->add($node);
	}

	public function removeNode(NodeEntry $node) : bool{
		return $this->data->remove($node);
	}

	public function clearNodes() : void{
		$this->data->clear();
	}

	/** Removes expired temporary nodes. Returns true if any were removed. */
	public function auditTemporaryNodes() : bool{
		return $this->data->auditTemporaryNodes();
	}

	/**
	 * Returns inheritance nodes (group.xxx) that are active (not expired).
	 *
	 * @return NodeEntry[]
	 */
	public function getOwnInheritanceNodes() : array{
		return array_values(
			array_filter($this->getActiveNodes(), static fn(NodeEntry $n) => str_starts_with($n->getKey(), 'group.') && $n->getValue())
		);
	}

	/**
	 * Returns a comparator for sorting the inherited groups of this holder.
	 *
	 * @return callable(Group, Group): int
	 */
	public function getInheritanceComparator() : callable{
		return InheritanceComparator::getFor($this);
	}

	/** @return NodeMapMutable */
	protected function getNodeMap() : NodeMapMutable{
		return $this->data;
	}
}

