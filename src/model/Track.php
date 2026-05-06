<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model;

use function array_filter;
use function array_map;
use function array_splice;
use function array_values;
use function in_array;
use function strtolower;

class Track{
	/** @var string[] */
	private array $groups = [];

	public function __construct(private string $name){ }

	public function getName() : string{
		return $this->name;
	}

	/** @return string[] */
	public function getGroups() : array{
		return $this->groups;
	}

	/** @param string[] $groups */
	public function setGroups(array $groups) : void{
		$this->groups = $groups;
	}

	public function appendGroup(string $group) : void{
		if(!in_array(strtolower($group), array_map('strtolower', $this->groups), true)){
			$this->groups[] = $group;
		}
	}

	public function insertGroup(string $group, int $position) : void{
		array_splice($this->groups, $position, 0, [$group]);
	}

	public function removeGroup(string $group) : void{
		$this->groups = array_values(array_filter($this->groups, static fn($g) => strtolower($g) !== strtolower($group)));
	}

	public function containsGroup(string $group) : bool{
		return in_array(strtolower($group), array_map('strtolower', $this->groups), true);
	}

	public function indexOf(string $group) : int{
		foreach($this->groups as $i => $g){
			if(strtolower($g) === strtolower($group)) return $i;
		}
		return -1;
	}
}

