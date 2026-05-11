<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model\nodemap;

use jasonw4331\LuckPerms\node\NodeEntry;
use function array_filter;
use function array_values;
use function count;
use function time;

/**
 * Base implementation of {@link NodeMap}.
 */
abstract class NodeMapBase extends NodeMap{
	/** @var NodeEntry[] */
	protected array $nodes = [];

	public function asList() : array{
		return $this->nodes;
	}

	public function asListActive() : array{
		$now = time();
		return array_values(
			array_filter($this->nodes, static fn(NodeEntry $n) => !$n->isTemporary() || $n->getExpiry() === null || $n->getExpiry() > $now)
		);
	}

	public function add(NodeEntry $node) : void{
		$this->nodes[] = $node;
	}

	public function remove(NodeEntry $node) : bool{
		foreach($this->nodes as $i => $n){
			if($n->getKey() === $node->getKey() && $n->getContext() === $node->getContext()){
				unset($this->nodes[$i]);
				$this->nodes = array_values($this->nodes);
				return true;
			}
		}
		return false;
	}

	public function clear() : void{
		$this->nodes = [];
	}

	/** @param NodeEntry[] $nodes */
	public function setNodes(array $nodes) : void{
		$this->nodes = array_values($nodes);
	}

	/** Removes expired temporary nodes. Returns true if any were removed. */
	public function auditTemporaryNodes() : bool{
		$before = count($this->nodes);
		$this->nodes = $this->asListActive();
		return count($this->nodes) < $before;
	}
}
