<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger;

use jasonw4331\LuckPerms\api\messenger\message\OutgoingMessage;

/**
 * Sends outgoing messages to other servers.
 */
interface Messenger
{
	public function sendOutgoingMessage(OutgoingMessage $outgoingMessage) : void;

	public function close() : void;
}
