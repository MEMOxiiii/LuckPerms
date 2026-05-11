<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event\model;

use jasonw4331\LuckPerms\api\actionlog\Action;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UnknownSource implements Action\Source{
private static ?self $instance = null;

public static function get() : self{
return self::$instance ??= new self();
}

public function getUniqueId() : UuidInterface{
return Uuid::fromString('00000000-0000-0000-0000-000000000000');
}

public function getName() : string{
return 'unknown';
}
}
