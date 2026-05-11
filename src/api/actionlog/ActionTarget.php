<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog;

use Ramsey\Uuid\UuidInterface;

/**
 * Represents the target of a logged action.
 */
interface ActionTarget
{
    public function getUniqueId(): ?UuidInterface;

    public function getName(): string;

    public function getType(): ActionTargetType;
}
