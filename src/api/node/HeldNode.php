<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\node;

/**
 * Represents a held node — a node associated with a holder identifier.
 *
 * @deprecated
 */
interface HeldNode
{
	/**
	 * Gets the holder's identifier (UUID string or group name).
	 */
	public function getHolder() : mixed;

	public function getNode() : Node;
}
