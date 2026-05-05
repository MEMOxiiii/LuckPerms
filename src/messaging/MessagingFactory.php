<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging;

use jasonw4331\LuckPerms\LuckPerms;

class MessagingFactory{
	public function __construct(private LuckPerms $plugin){ }

	public function getInstance() : ?InternalMessagingService{
		return null;
	}

}
