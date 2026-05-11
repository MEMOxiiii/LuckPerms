<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog;

enum ActionTargetType: string
{
    case USER  = 'USER';
    case GROUP = 'GROUP';
    case TRACK = 'TRACK';
}
