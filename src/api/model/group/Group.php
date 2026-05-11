<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\group;

use jasonw4331\LuckPerms\api\model\PermissionHolder;
use jasonw4331\LuckPerms\api\query\QueryOptions;

/**
 * An inheritable holder of permission data.
 */
interface Group extends PermissionHolder
{
    public function getName(): string;

    public function getDisplayName(): ?string;

    public function getDisplayNameWithOptions(QueryOptions $queryOptions): ?string;

    public function getWeight(): ?int;
}
