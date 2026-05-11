<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\platform;

use Ramsey\Uuid\UuidInterface;

/**
 * Represents an entity on the server (player or console).
 */
interface PlatformEntity
{
    public function getUniqueId(): ?UuidInterface;

    public function getName(): string;

    public function getType(): PlatformEntityType;
}
