<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\messenger;

/**
 * Provides Messenger instances.
 */
interface MessengerProvider
{
    public function getName(): string;

    public function obtain(IncomingMessageConsumer $incomingMessageConsumer): Messenger;
}
