<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model;

use jasonw4331\LuckPerms\cacheddata\GroupCachedDataManager;
use jasonw4331\LuckPerms\node\NodeEntry;

class Group{
        private GroupCachedDataManager $cachedData;
        /** @var NodeEntry[] */
        private array $nodes = [];
        private int $weight = 0;
        private ?string $displayName = null;

	public function __construct(private string $name){
		$this->cachedData = new GroupCachedDataManager();
	}

	public function getName() : string{
		return $this->name;
	}

	public function getWeight() : int{
		return $this->weight;
	}

	public function setWeight(int $weight) : void{
		$this->weight = $weight;
	}

	public function getDisplayName() : ?string{
		return $this->displayName;
	}

	public function setDisplayName(?string $displayName) : void{
		$this->displayName = $displayName;
	}

	public function getCachedData() : GroupCachedDataManager{
		return $this->cachedData;
	}

/** @return NodeEntry[] */
        public function getNodes() : array{
                return $this->nodes;
        }

        /** @param NodeEntry[] $nodes */
        public function setNodes(array $nodes) : void{
                $this->nodes = $nodes;
        }

        public function addNode(NodeEntry $node) : void{
                $this->nodes[] = $node;
        }

        public function auditTemporaryNodes() : bool{
		return false;
	}
}
