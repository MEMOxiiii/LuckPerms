<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\platform;

/**
 * Represents the health status of a LuckPerms implementation.
 */
interface Health
{
    public function isHealthy(): bool;

    /**
     * @return array<string, mixed>
     */
    public function getDetails(): array;
}
