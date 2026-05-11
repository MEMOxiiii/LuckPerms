<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog;

use Ramsey\Uuid\UuidInterface;

/**
 * Represents a logged action.
 */
interface Action
{
    public function getTimestamp(): \DateTimeImmutable;

    public function getSource(): ActionSource;

    public function getTarget(): ActionTarget;

    public function getDescription(): string;

    public function toBuilder(): ActionBuilder;
}
