<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\event;

/**
 * Represents a subscription to a LuckPermsEvent.
 */
interface EventSubscription
{
	public function getEventClass() : string;

	public function isActive() : bool;

	public function close() : void;

	public function getHandler() : callable;
}
