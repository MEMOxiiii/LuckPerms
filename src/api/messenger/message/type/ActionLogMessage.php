<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger\message\type;

use jasonw4331\LuckPerms\api\actionlog\Action;
use jasonw4331\LuckPerms\api\messenger\message\Message;

interface ActionLogMessage extends Message
{
	public function getAction() : Action;
}
