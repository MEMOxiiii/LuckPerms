<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\cacheddata;

use jasonw4331\LuckPerms\api\node\Node;

/**
 * Represents the result of a cached data lookup.
 *
 * @since 5.4
 */
interface Result
{
    /**
     * Gets the underlying result.
     */
    public function result(): mixed;

    /**
     * Gets the node that caused the result.
     */
    public function node(): ?Node;
}
