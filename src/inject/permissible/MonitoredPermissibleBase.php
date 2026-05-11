<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\permissible;

use pocketmine\permission\PermissibleBase;
use function call_user_func;

class MonitoredPermissibleBase{
/** @var list<callable> */
private static array $callbacks = [];

public static function register(callable $callback) : void{
self::$callbacks[] = $callback;
}

public static function onPermissionChange(string $permission) : void{
foreach(self::$callbacks as $cb){
call_user_func($cb, $permission);
}
}
}
