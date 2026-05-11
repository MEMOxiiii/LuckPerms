<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\track;

use jasonw4331\LuckPerms\api\util\Result;

/**
 * Encapsulates the result of a User's demotion along a Track.
 */
interface DemotionResult extends Result
{
	public function getStatus() : DemotionStatus;

	public function wasSuccessful() : bool;

	public function getGroupFrom() : ?string;

	public function getGroupTo() : ?string;
}
