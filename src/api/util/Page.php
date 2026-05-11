<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\util;

/**
 * Represents a page of results.
 */
interface Page
{
	/**
	 * @return array<mixed>
	 */
	public function entries() : array;

	public function overallSize() : int;
}
