<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model\manager\group;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group;

class StandardGroupManager{
	/** @var array<string, Group> */
	private array $loaded = [];

	public function __construct(private LuckPerms $plugin){ }

	/** @return array<int, Group> */
	public function getAll() : array{
		return array_values($this->loaded);
	}

	public function getOrMake(string $name) : Group{
		$key = strtolower($name);
		return $this->loaded[$key] ??= new Group($name);
	}

	public function getIfLoaded(string $name) : ?Group{
		return $this->loaded[strtolower($name)] ?? null;
	}

	public function delete(string $name) : void{
		unset($this->loaded[strtolower($name)]);
	}

	public function invalidateAllGroupCaches() : void{
		foreach($this->loaded as $group){
			$group->getCachedData()->invalidate();
		}
	}
}
