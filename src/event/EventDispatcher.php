<?php


declare(strict_types=1);

namespace jasonw4331\LuckPerms\event;

use jasonw4331\LuckPerms\EventBus;

class EventDispatcher{
	public function __construct(private EventBus $eventBus){ }

	public function getEventBus() : EventBus{
		return $this->eventBus;
	}

	public function dispatchUniqueIdLookup(string $username, mixed $uniqueId) : mixed{
		return $uniqueId;
	}

	public function dispatchUsernameLookup(mixed $uniqueId, ?string $username) : ?string{
		return $username;
	}

	public function dispatchUsernameValidityCheck(string $username, bool $valid) : bool{
		return $valid;
	}
}
