<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms;

use jasonw4331\LuckPerms\tasks\SyncTask;

final class Buffer{
	public function __construct(private LuckPerms $plugin){ }

	public function request() : void{
		(new SyncTask($this->plugin))->run();
	}
}
