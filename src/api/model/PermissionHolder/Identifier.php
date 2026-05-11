<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\PermissionHolder;

/**
 * Represents a way to identify distinct PermissionHolders.
 */
interface Identifier
{
    public const USER_TYPE = 'user';
    public const GROUP_TYPE = 'group';

    public function getName(): string;

    public function getType(): string;
}
