<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Provides the default context keys used by LuckPerms.
 */
final class DefaultContextKeys
{
    public const SERVER_KEY         = 'server';
    public const WORLD_KEY          = 'world';
    public const DIMENSION_TYPE_KEY = 'dimension-type';
    public const GAMEMODE_KEY       = 'gamemode';

    private function __construct()
    {
    }
}
