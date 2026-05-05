<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms;

class EventBus{
	public function __construct(private LuckPerms $plugin, private mixed $provider = null){ }

	public function subscribe(object $listener) : void{
	}

}
