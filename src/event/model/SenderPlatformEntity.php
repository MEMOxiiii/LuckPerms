<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event\model;

use jasonw4331\LuckPerms\api\platform\PlatformEntity;
use jasonw4331\LuckPerms\sender\Sender;
use Ramsey\Uuid\UuidInterface;

class SenderPlatformEntity implements PlatformEntity{
public function __construct(private Sender $sender){}

public function getUniqueId() : UuidInterface{
return $this->sender->getUniqueId();
}

public function getName() : string{
return $this->sender->getName();
}
}
