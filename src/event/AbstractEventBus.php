<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event;

use jasonw4331\LuckPerms\EventBus;
use jasonw4331\LuckPerms\api\event\LuckPermsEvent;
use jasonw4331\LuckPerms\api\event\EventSubscription;
use Closure;
use function is_a;

abstract class AbstractEventBus implements EventBus{
/** @var array<class-string, list<array{handler: Closure, plugin: mixed}>> */
private array $subscriptions = [];

public function subscribe(string $eventClass, Closure $handler, mixed $plugin = null) : EventSubscription{
$this->subscriptions[$eventClass][] = ['handler' => $handler, 'plugin' => $plugin];
return new LuckPermsEventSubscription($this, $eventClass, $handler);
}

public function unsubscribe(string $eventClass, Closure $handler) : void{
if(isset($this->subscriptions[$eventClass])){
$this->subscriptions[$eventClass] = array_values(
array_filter($this->subscriptions[$eventClass], fn($s) => $s['handler'] !== $handler)
);
}
}

public function post(LuckPermsEvent $event) : void{
$eventClass = $event::class;
foreach($this->subscriptions as $cls => $subs){
if(is_a($event, $cls)){
foreach($subs as $sub){
($sub['handler'])($event);
}
}
}
}
}
