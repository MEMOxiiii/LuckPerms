<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\platform;

use jasonw4331\LuckPerms\api\cacheddata\CachedMetaData;
use jasonw4331\LuckPerms\api\cacheddata\CachedPermissionData;
use jasonw4331\LuckPerms\api\context\ImmutableContextSet;
use jasonw4331\LuckPerms\api\model\user\User;
use jasonw4331\LuckPerms\api\query\QueryOptions;

/**
 * A utility interface for adapting platform Player instances to LuckPerms Users.
 */
interface PlayerAdapter
{
    public function getUser(mixed $player): User;

    public function getContext(mixed $player): ImmutableContextSet;

    public function getQueryOptions(mixed $player): QueryOptions;

    public function getPermissionData(mixed $player): CachedPermissionData;

    public function getMetaData(mixed $player): CachedMetaData;
}
