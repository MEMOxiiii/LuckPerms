<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger\message;

/**
 * Represents a message which can be sent outward.
 */
interface OutgoingMessage extends Message
{
    public function asEncodedString(): string;
}
