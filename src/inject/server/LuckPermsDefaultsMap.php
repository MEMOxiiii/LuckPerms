<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\server;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;

class LuckPermsDefaultsMap{
/** @var array<string, Permission> */
private array $defaults = [];

public function add(Permission $permission) : void{
$this->defaults[$permission->getName()] = $permission;
}

public function remove(string $name) : void{
unset($this->defaults[$name]);
}

public function get(string $name) : ?Permission{
return $this->defaults[$name] ?? null;
}

/** @return array<string, Permission> */
public function getAll() : array{
return $this->defaults;
}
}
