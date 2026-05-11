<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\server;

use pocketmine\permission\Permission;
use function strtolower;

class LuckPermsPermissionMap{
/** @var array<string, Permission> */
private array $permissions = [];

public function register(Permission $permission) : bool{
$name = strtolower($permission->getName());
if(isset($this->permissions[$name])) return false;
$this->permissions[$name] = $permission;
return true;
}

public function unregister(string $name) : bool{
$name = strtolower($name);
if(!isset($this->permissions[$name])) return false;
unset($this->permissions[$name]);
return true;
}

public function get(string $name) : ?Permission{
return $this->permissions[strtolower($name)] ?? null;
}

/** @return array<string, Permission> */
public function getAll() : array{
return $this->permissions;
}
}
