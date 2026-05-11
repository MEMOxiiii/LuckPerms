<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inheritance;

use jasonw4331\LuckPerms\graph\Graph;
use jasonw4331\LuckPerms\graph\TraversalAlgorithm;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\model\User;
use function str_starts_with;
use function substr;
use function usort;

/**
 * Represents an inheritance graph used to traverse the permission holder hierarchy.
 */
class InheritanceGraph implements Graph{
	/** @var callable(string): ?Group */
	private $groupResolver;

	/** @param callable(string): ?Group $groupResolver */
	public function __construct(
		private TraversalAlgorithm $algorithm,
		private bool $postTraversalSort,
		callable $groupResolver
	){
		$this->groupResolver = $groupResolver;
	}

	/**
	 * Returns the direct parents (successors) of a given holder.
	 *
	 * @param User|Group $holder
	 * @return Group[]
	 */
	public function successors(mixed $holder) : array{
		$successors = [];
		$seen = [];
		foreach($holder->getNodes() as $node){
			// inheritance nodes have keys like "group.groupname"
			if(!str_starts_with($node->getKey(), 'group.')){
				continue;
			}
			if($node->isTemporary()){
				continue; // skip expired nodes (caller should have audited already)
			}
			$groupName = substr($node->getKey(), 6);
			if(isset($seen[$groupName])){
				continue;
			}
			$seen[$groupName] = true;
			$g = ($this->groupResolver)($groupName);
			if($g !== null){
				$successors[] = $g;
			}
		}
		$comparator = InheritanceComparator::getFor($holder);
		usort($successors, $comparator);
		return $successors;
	}

	/**
	 * Traverse the inheritance graph starting from $holder using the configured algorithm.
	 *
	 * @param User|Group $holder
	 * @return array<User|Group>
	 */
	public function traverse(mixed $holder) : array{
		$result = $this->algorithm->traverse($this, $holder);
		if($this->postTraversalSort){
			$comparator = InheritanceComparator::getFor($holder);
			usort($result, $comparator);
		}
		return $result;
	}
}
