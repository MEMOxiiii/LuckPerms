<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event;

use jasonw4331\LuckPerms\api\event\EventSubscription;

class LuckPermsEventSubscription implements EventSubscription{
private bool $active = true;

public function __construct(
private AbstractEventBus $bus,
private string $eventClass,
private mixed $handler
){}

public function getEventClass() : string{
return $this->eventClass;
}

public function getHandler() : callable{
return $this->handler;
}

public function isActive() : bool{
return $this->active;
}

public function close() : void{
if($this->active){
$this->active = false;
$this->bus->unsubscribe($this->eventClass, $this->handler);
}
}
}
