<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\extension;

use jasonw4331\LuckPerms\LuckPerms;

class SimpleExtensionManager{
	public function __construct(private LuckPerms $plugin){ }

	public function loadExtensions(string $directory) : void{
	}

	public function close() : void{
	}
}
