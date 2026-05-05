<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model\manager\user;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\User;
use Ramsey\Uuid\UuidInterface;

class StandardUserManager{
	/** @var array<string, User> */
	private array $loaded = [];

	public function __construct(private LuckPerms $plugin){ }

	public function load(UuidInterface $uniqueId, string $username) : User{
		$key = $uniqueId->toString();
		return $this->loaded[$key] ??= new User($uniqueId, $username);
	}

	public function getIfLoaded(UuidInterface $uniqueId) : ?User{
		return $this->loaded[$uniqueId->toString()] ?? null;
	}

	/** @return array<int, User> */
	public function getAll() : array{
		return array_values($this->loaded);
	}

	public function unload(UuidInterface $uniqueId) : void{
		unset($this->loaded[$uniqueId->toString()]);
	}

	public function invalidateAllUserCaches() : void{
		foreach($this->loaded as $user){
			$user->getCachedData()->invalidate();
		}
	}
}
