<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\storage;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\model\User;
use Ramsey\Uuid\UuidInterface;

class Storage{
	/** @var array<string, string> */
	private array $uuidToName = [];
	/** @var array<string, string> */
	private array $nameToUuid = [];

	public function __construct(private LuckPerms $plugin){ }

	public function saveUser(User $user) : void{
		$uuid = $user->getUniqueId()->toString();
		$name = strtolower($user->getUsername());
		$this->uuidToName[$uuid] = $user->getUsername();
		$this->nameToUuid[$name] = $uuid;
	}

	public function saveGroup(Group $group) : void{
		// no-op for the current lightweight boot path
	}

	public function getPlayerUniqueId(string $username) : ?UuidInterface{
		$uuid = $this->nameToUuid[strtolower($username)] ?? null;
		return $uuid !== null ? \Ramsey\Uuid\Uuid::fromString($uuid) : null;
	}

	public function getPlayerName(UuidInterface|string $uniqueId) : ?string{
		$key = $uniqueId instanceof UuidInterface ? $uniqueId->toString() : $uniqueId;
		return $this->uuidToName[$key] ?? null;
	}

	public function shutdown() : void{
		// no-op for in-memory storage
	}

}
