<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\permissible;

use pocketmine\permission\PermissibleBase;
use pocketmine\player\Player;

class DummyPermissibleBase{
public static function create() : PermissibleBase{
return new PermissibleBase(null);
}
}
