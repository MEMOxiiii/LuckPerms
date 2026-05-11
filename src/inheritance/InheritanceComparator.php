<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inheritance;

use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\model\User;

/**
 * Determines the order of group inheritance.
 * Groups with higher weight come later in the list (higher priority).
 */
class InheritanceComparator{
	private ?User $origin;

	public function __construct(?User $origin){
		$this->origin = $origin;
	}

	/**
	 * Returns a comparator callable for the given origin holder.
	 *
	 * @param User|Group $origin
	 * @return callable(Group, Group): int
	 */
	public static function getFor(mixed $origin) : callable{
		if($origin instanceof User){
			$comparator = new self($origin);
			return static fn(Group $a, Group $b) : int => -$comparator->compare($a, $b);
		}
		$comparator = new self(null);
		return static fn(Group $a, Group $b) : int => -$comparator->compare($a, $b);
	}

	/**
	 * @return int negative if o1 < o2, 0 if equal, positive if o1 > o2
	 */
	public function compare(Group $o1, Group $o2) : int{
		$weightDiff = $o1->getWeight() <=> $o2->getWeight();
		if($weightDiff !== 0){
			return $weightDiff;
		}
		// check primary group status
		if($this->origin !== null){
			$primaryGroup = $this->origin->getPrimaryGroup();
			$o1IsPrimary = $primaryGroup !== null && $primaryGroup === $o1->getName();
			$o2IsPrimary = $primaryGroup !== null && $primaryGroup === $o2->getName();
			return $o1IsPrimary <=> $o2IsPrimary;
		}
		// fallback: stable (unchanged order)
		return 0;
	}
}
