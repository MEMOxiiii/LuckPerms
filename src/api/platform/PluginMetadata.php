<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\platform;

/**
 * Provides information about the LuckPerms plugin.
 */
interface PluginMetadata
{
    public function getVersion(): string;

    public function getApiVersion(): string;
}
