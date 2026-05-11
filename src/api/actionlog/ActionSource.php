<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog;

use Ramsey\Uuid\UuidInterface;

/**
 * Represents the source of a logged action.
 */
interface ActionSource
{
	public function getUniqueId() : UuidInterface;

	public function getName() : string;
}
