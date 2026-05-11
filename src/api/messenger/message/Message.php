<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger\message;

use Ramsey\Uuid\UuidInterface;

/**
 * Represents a message sent through the messaging service.
 */
interface Message
{
	public function getId() : UuidInterface;
}
