<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\event;

/**
 * The base interface for events fired by LuckPerms.
 */
interface LuckPermsEvent
{
    public function getLuckPerms(): \jasonw4331\LuckPerms\api\LuckPerms;

    public function getEventType(): string;
}
