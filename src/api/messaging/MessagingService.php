<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messaging;

use jasonw4331\LuckPerms\api\model\user\User;

/**
 * Provides access to the messaging service.
 */
interface MessagingService
{
	public function getName() : string;

	public function pushUpdate() : void;

	public function pushUserUpdate(User $user) : void;

	public function sendCustomMessage(string $channelId, string $payload) : void;
}
