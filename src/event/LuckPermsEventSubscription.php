<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event;

use jasonw4331\LuckPerms\api\event\EventSubscription;
use Closure;

class LuckPermsEventSubscription implements EventSubscription{
public function __construct(
private AbstractEventBus $bus,
private string $eventClass,
private Closure $handler
){}

public function getEventClass() : string{
return $this->eventClass;
}

public function getHandler() : Closure{
return $this->handler;
}

public function close() : void{
$this->bus->unsubscribe($this->eventClass, $this->handler);
}
}
