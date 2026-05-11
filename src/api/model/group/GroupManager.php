<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\group;

use jasonw4331\LuckPerms\api\node\HeldNode;
use jasonw4331\LuckPerms\api\node\matcher\NodeMatcher;

/**
 * Responsible for managing Group instances.
 */
interface GroupManager
{
	public function createAndLoadGroup(string $name) : Group;

	public function loadGroup(string $name) : ?Group;

	public function saveGroup(Group $group) : void;

	public function deleteGroup(Group $group) : void;

	public function modifyGroup(string $name, callable $action) : void;

	public function loadAllGroups() : void;

	/**
	 * @return array<string, array<\jasonw4331\LuckPerms\api\node\Node>>
	 */
	public function searchAll(NodeMatcher $matcher) : array;

	/**
	 * @deprecated Use searchAll() instead
	 * @return HeldNode[]
	 */
	public function getWithPermission(string $permission) : array;

	public function getGroup(string $name) : ?Group;

	/**
	 * @return Group[]
	 */
	public function getLoadedGroups() : array;

	public function isLoaded(string $name) : bool;
}
