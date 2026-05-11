<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event;

use jasonw4331\LuckPerms\api\event\EventBus;
use jasonw4331\LuckPerms\api\event\EventSubscription;
use jasonw4331\LuckPerms\api\event\LuckPermsEvent;
use function array_filter;
use function array_map;
use function array_values;
use function is_a;

abstract class AbstractEventBus implements EventBus{
/** @var array<class-string, list<array{handler: callable, plugin: ?object}>> */
private array $subscriptions = [];

public function subscribe(string $eventClass, callable $handler) : EventSubscription{
$this->subscriptions[$eventClass][] = ['handler' => $handler, 'plugin' => null];
return new LuckPermsEventSubscription($this, $eventClass, $handler);
}

public function subscribeWithPlugin(object $plugin, string $eventClass, callable $handler) : EventSubscription{
$this->subscriptions[$eventClass][] = ['handler' => $handler, 'plugin' => $plugin];
return new LuckPermsEventSubscription($this, $eventClass, $handler);
}

public function getSubscriptions(string $eventClass) : array{
return array_map(
fn($s) => new LuckPermsEventSubscription($this, $eventClass, $s['handler']),
$this->subscriptions[$eventClass] ?? []
);
}

public function unsubscribe(string $eventClass, callable $handler) : void{
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
