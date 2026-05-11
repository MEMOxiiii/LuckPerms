<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\extension;

/**
 * Represents a LuckPerms extension.
 */
interface Extension
{
    public function load(): void;

    public function unload(): void;
}
