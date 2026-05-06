<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model;

use jasonw4331\LuckPerms\cacheddata\UserCachedDataManager;
use jasonw4331\LuckPerms\node\NodeEntry;
use Ramsey\Uuid\UuidInterface;
use function array_filter;
use function array_values;
use function count;
use function time;

class User{
        private UserCachedDataManager $cachedData;
        /** @var NodeEntry[] */
        private array $nodes = [];
	public function __construct(private UuidInterface $uniqueId, private string $username){
		$this->cachedData = new UserCachedDataManager();
	}

	public function getUniqueId() : UuidInterface{
		return $this->uniqueId;
	}

	public function getUsername() : string{
		return $this->username;
	}

	public function getCachedData() : UserCachedDataManager{
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
		$now = time();
		$before = count($this->nodes);
		$this->nodes = array_values(array_filter($this->nodes, static fn(NodeEntry $n) => !$n->isTemporary() || $n->getExpiry() === null || $n->getExpiry() > $now));
		return count($this->nodes) < $before;
	}

}
