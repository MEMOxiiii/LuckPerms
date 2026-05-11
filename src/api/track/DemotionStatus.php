<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\track;

use jasonw4331\LuckPerms\api\util\Result;

enum DemotionStatus: string implements Result
{
    case SUCCESS                   = 'SUCCESS';
    case REMOVED_FROM_FIRST_GROUP  = 'REMOVED_FROM_FIRST_GROUP';
    case MALFORMED_TRACK           = 'MALFORMED_TRACK';
    case NOT_ON_TRACK              = 'NOT_ON_TRACK';
    case AMBIGUOUS_CALL            = 'AMBIGUOUS_CALL';
    case UNDEFINED_FAILURE         = 'UNDEFINED_FAILURE';

    public function wasSuccessful(): bool
    {
        return $this === self::SUCCESS || $this === self::REMOVED_FROM_FIRST_GROUP;
    }
}
