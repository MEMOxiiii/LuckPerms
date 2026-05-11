<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event\gen;

use jasonw4331\LuckPerms\api\event\LuckPermsEvent;

abstract class AbstractEvent implements LuckPermsEvent{
private bool $cancelled = false;

public function isCancelled() : bool{
return $this->cancelled;
}

public function setCancelled(bool $cancelled) : void{
$this->cancelled = $cancelled;
}
}
