<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog;

use Ramsey\Uuid\UuidInterface;

/**
 * Represents the log of LuckPerms actions.
 *
 * @deprecated Use ActionLogger::queryActions instead.
 */
interface ActionLog
{
    /**
     * @return Action[]
     */
    public function getContent(): array;

    /**
     * @return Action[]
     */
    public function getContentByActor(UuidInterface $actor): array;

    /**
     * @return Action[]
     */
    public function getUserHistory(UuidInterface $uniqueId): array;

    /**
     * @return Action[]
     */
    public function getGroupHistory(string $name): array;

    /**
     * @return Action[]
     */
    public function getTrackHistory(string $name): array;
}
