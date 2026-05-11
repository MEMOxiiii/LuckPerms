<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\track;

use jasonw4331\LuckPerms\api\util\Result;

/**
 * Encapsulates the result of a User's promotion along a Track.
 */
interface PromotionResult extends Result
{
	public function getStatus() : PromotionStatus;

	public function wasSuccessful() : bool;

	public function getGroupFrom() : ?string;

	public function getGroupTo() : ?string;
}
