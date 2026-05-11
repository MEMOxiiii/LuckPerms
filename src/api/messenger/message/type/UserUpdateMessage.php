<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger\message\type;

use jasonw4331\LuckPerms\api\messenger\message\Message;
use Ramsey\Uuid\UuidInterface;

interface UserUpdateMessage extends Message
{
    public function getUserUniqueId(): UuidInterface;
}
