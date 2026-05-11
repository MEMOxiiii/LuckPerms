<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\data;

use jasonw4331\LuckPerms\api\node\Node;
use jasonw4331\LuckPerms\api\util\Result;

/**
 * Represents the result of a data mutation call on a LuckPerms object.
 */
enum DataMutateResult: string implements Result
{
    case SUCCESS           = 'SUCCESS';
    case FAIL              = 'FAIL';
    case FAIL_ALREADY_HAS  = 'FAIL_ALREADY_HAS';
    case FAIL_LACKS        = 'FAIL_LACKS';

    public function wasSuccessful(): bool
    {
        return $this === self::SUCCESS;
    }
}
