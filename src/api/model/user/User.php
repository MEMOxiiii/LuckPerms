<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\user;

use jasonw4331\LuckPerms\api\model\data\DataMutateResult;
use jasonw4331\LuckPerms\api\model\PermissionHolder;
use Ramsey\Uuid\UuidInterface;

/**
 * A player which holds permission data.
 */
interface User extends PermissionHolder
{
	public function getUniqueId() : UuidInterface;

	public function getUsername() : ?string;

	public function getPrimaryGroup() : string;

	public function setPrimaryGroup(string $group) : DataMutateResult;
}
