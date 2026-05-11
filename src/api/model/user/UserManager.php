<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\user;

use jasonw4331\LuckPerms\api\model\PlayerSaveResult;
use jasonw4331\LuckPerms\api\node\HeldNode;
use jasonw4331\LuckPerms\api\node\matcher\NodeMatcher;
use Ramsey\Uuid\UuidInterface;

/**
 * Responsible for managing User instances.
 */
interface UserManager
{
	public function loadUser(UuidInterface $uniqueId, ?string $username = null) : User;

	public function lookupUniqueId(string $username) : ?UuidInterface;

	public function lookupUsername(UuidInterface $uniqueId) : ?string;

	public function saveUser(User $user) : void;

	public function modifyUser(UuidInterface $uniqueId, callable $action) : void;

	public function savePlayerData(UuidInterface $uniqueId, string $username) : PlayerSaveResult;

	public function deletePlayerData(UuidInterface $uniqueId) : void;

	/**
	 * @return UuidInterface[]
	 */
	public function getUniqueUsers() : array;

	/**
	 * @return array<string, array<\jasonw4331\LuckPerms\api\node\Node>>
	 */
	public function searchAll(NodeMatcher $matcher) : array;

	/**
	 * @deprecated Use searchAll() instead
	 * @return HeldNode[]
	 */
	public function getWithPermission(string $permission) : array;

	public function getUser(UuidInterface|string $identifier) : ?User;

	/**
	 * @return User[]
	 */
	public function getLoadedUsers() : array;

	public function isLoaded(UuidInterface $uniqueId) : bool;

	public function cleanupUser(User $user) : void;
}
