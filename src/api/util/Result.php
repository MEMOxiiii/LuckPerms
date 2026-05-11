<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\util;

/**
 * Represents the result of an operation.
 */
interface Result
{
    public function wasSuccessful(): bool;
}
