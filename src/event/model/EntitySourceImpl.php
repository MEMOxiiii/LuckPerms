<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\event\model;

use jasonw4331\LuckPerms\api\actionlog\Action;
use jasonw4331\LuckPerms\sender\Sender;
use Ramsey\Uuid\UuidInterface;

class EntitySourceImpl implements Action\Source{
private UuidInterface $uniqueId;
private string $name;

public function __construct(UuidInterface $uniqueId, string $name){
$this->uniqueId = $uniqueId;
$this->name = $name;
}

public static function fromSender(Sender $sender) : self{
return new self($sender->getUniqueId(), $sender->getName());
}

public function getUniqueId() : UuidInterface{
return $this->uniqueId;
}

public function getName() : string{
return $this->name;
}
}
