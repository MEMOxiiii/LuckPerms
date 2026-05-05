<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\EventBus;
use jasonw4331\LuckPerms\context\ContextManager;

class LuckPermsApiProvider{
	public function __construct(private LuckPerms $plugin){ }

	public function getEventBus() : EventBus{
		return $this->plugin->getEventDispatcher()->getEventBus();
	}

	public function getContextManager() : ContextManager{
		return $this->plugin->getContextManager();
	}
}
