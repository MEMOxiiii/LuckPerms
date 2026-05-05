<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model\manager\track;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Track;

class StandardTrackManager{
	/** @var array<string, Track> */
	private array $loaded = [];

	public function __construct(private LuckPerms $plugin){ }

	/** @return array<int, Track> */
	public function getAll() : array{
		return array_values($this->loaded);
	}

	public function getOrMake(string $name) : Track{
		$key = strtolower($name);
		return $this->loaded[$key] ??= new Track($name);
	}

	public function delete(string $name) : void{
		unset($this->loaded[strtolower($name)]);
	}
}
