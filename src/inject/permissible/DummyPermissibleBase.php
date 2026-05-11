<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\permissible;

use pocketmine\permission\PermissibleBase;

class DummyPermissibleBase{
public static function create() : PermissibleBase{
return new PermissibleBase(null);
}
}
