<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger\message\type;

use jasonw4331\LuckPerms\api\messenger\message\Message;

interface CustomMessage extends Message
{
	public function getChannelId() : string;

	public function getPayload() : string;
}
