<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\track;

use jasonw4331\LuckPerms\api\context\ContextSet;
use jasonw4331\LuckPerms\api\Group;
use jasonw4331\LuckPerms\api\model\data\DataMutateResult;
use jasonw4331\LuckPerms\api\model\user\User;

/**
 * An ordered chain of Groups.
 */
interface Track
{
	public function getName() : string;

	/**
	 * @return string[]
	 */
	public function getGroups() : array;

	public function getNext(Group $current) : ?string;

	public function getPrevious(Group $current) : ?string;

	public function promote(User $user, ContextSet $contextSet) : PromotionResult;

	public function demote(User $user, ContextSet $contextSet) : DemotionResult;

	public function appendGroup(Group $group) : DataMutateResult;

	public function insertGroup(Group $group, int $position) : DataMutateResult;

	public function removeGroup(Group|string $group) : DataMutateResult;

	public function containsGroup(Group|string $group) : bool;

	public function isEmpty() : bool;

	public function size() : int;
}
