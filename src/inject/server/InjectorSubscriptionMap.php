<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\server;

use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\scheduler\Task;

class InjectorSubscriptionMap extends Task{

	public function __construct(LuckPerms $param){ }

	public function run() : void{ }

	public function onRun() : void{ }

	public static function uninject() : void{ }
}
