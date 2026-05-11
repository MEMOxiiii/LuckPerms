<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger;

use jasonw4331\LuckPerms\api\messenger\message\Message;

/**
 * Accepts incoming messages.
 */
interface IncomingMessageConsumer
{
	public function consumeIncomingMessage(Message $message) : bool;

	public function consumeIncomingMessageAsString(string $encodedString) : bool;
}
