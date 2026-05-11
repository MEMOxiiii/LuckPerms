<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\dependencies\relocation;

class RelocationHelper{
private static ?RelocationHandler $handler = null;

public static function getHandler() : RelocationHandler{
return self::$handler ??= new RelocationHandler();
}
}
