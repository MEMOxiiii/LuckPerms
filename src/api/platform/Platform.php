<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\platform;

use Ramsey\Uuid\UuidInterface;

/**
 * Provides information about the platform LuckPerms is running on.
 */
interface Platform
{
    public function getType(): PlatformType;

    /**
     * @return UuidInterface[]
     */
    public function getUniqueConnections(): array;

    /**
     * @return string[]
     */
    public function getKnownPermissions(): array;

    public function getStartTime(): \DateTimeImmutable;
}
