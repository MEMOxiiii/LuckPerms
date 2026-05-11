<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model\nodemap;

use jasonw4331\LuckPerms\node\NodeEntry;
use function count;

/**
 * A node map that records the source (holder name) of each node.
 * Used for inheritance resolution to track where each node comes from.
 */
class RecordedNodeMap extends NodeMapBase{
	/** @var array<int, string> index => source holder name */
	private array $sources = [];

	public function addFromSource(NodeEntry $node, string $source) : void{
		$idx = count($this->nodes);
		$this->nodes[] = $node;
		$this->sources[$idx] = $source;
	}

	/** @return array<int, string> */
	public function getSources() : array{
		return $this->sources;
	}

	public function getSourceFor(int $index) : ?string{
		return $this->sources[$index] ?? null;
	}

	public function clear() : void{
		parent::clear();
		$this->sources = [];
	}
}
