<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\server;

use pocketmine\permission\Permissible;
use function array_filter;
use function array_values;

class LuckPermsSubscriptionMap{
/** @var array<string, list<Permissible>> */
private array $subscriptions = [];

public function subscribe(string $permission, Permissible $permissible) : void{
$this->subscriptions[$permission][] = $permissible;
}

public function unsubscribe(string $permission, Permissible $permissible) : void{
if(isset($this->subscriptions[$permission])){
$this->subscriptions[$permission] = array_values(
array_filter($this->subscriptions[$permission], fn($p) => $p !== $permissible)
);
}
}

/** @return list<Permissible> */
public function getSubscriptions(string $permission) : array{
return $this->subscriptions[$permission] ?? [];
}
}
