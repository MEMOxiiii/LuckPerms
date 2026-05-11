<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\track;

use jasonw4331\LuckPerms\api\util\Result;

enum PromotionStatus: string implements Result
{
    case SUCCESS             = 'SUCCESS';
    case ADDED_TO_FIRST_GROUP = 'ADDED_TO_FIRST_GROUP';
    case MALFORMED_TRACK     = 'MALFORMED_TRACK';
    case END_OF_TRACK        = 'END_OF_TRACK';
    case AMBIGUOUS_CALL      = 'AMBIGUOUS_CALL';
    case UNDEFINED_FAILURE   = 'UNDEFINED_FAILURE';

    public function wasSuccessful(): bool
    {
        return $this === self::SUCCESS || $this === self::ADDED_TO_FIRST_GROUP;
    }
}
