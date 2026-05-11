<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\data;

/**
 * Represents a type of data.
 */
enum DataType: string
{
    /** Normal persistent data. */
    case NORMAL = 'NORMAL';

    /** Transient data that expires at end of session and is never saved. */
    case TRANSIENT = 'TRANSIENT';
}
