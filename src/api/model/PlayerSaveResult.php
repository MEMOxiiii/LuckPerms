<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model;

/**
 * Encapsulates the result of an operation to save uuid data about a player.
 */
interface PlayerSaveResult
{
    /**
     * @return Outcome[]
     */
    public function getOutcomes(): array;

    public function includes(Outcome $outcome): bool;

    public function getPreviousUsername(): ?string;

    /**
     * @return string[]|null
     */
    public function getOtherUniqueIds(): ?array;
}
