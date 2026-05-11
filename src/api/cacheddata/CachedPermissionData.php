<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\cacheddata;

use jasonw4331\LuckPerms\api\util\Tristate;

/**
 * Holds cached permission lookup data for a specific set of contexts.
 */
interface CachedPermissionData extends CachedData
{
    public function queryPermission(string $permission): Result;

    public function checkPermission(string $permission): Tristate;

    public function invalidateCache(): void;

    /**
     * @return array<string, bool>
     */
    public function getPermissionMap(): array;
}
