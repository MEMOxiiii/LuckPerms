<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\track;

/**
 * Responsible for managing Track instances.
 */
interface TrackManager
{
    public function createAndLoadTrack(string $name): Track;

    public function loadTrack(string $name): ?Track;

    public function saveTrack(Track $track): void;

    public function deleteTrack(Track $track): void;

    public function modifyTrack(string $name, callable $action): void;

    public function loadAllTracks(): void;

    public function getTrack(string $name): ?Track;

    /**
     * @return Track[]
     */
    public function getLoadedTracks(): array;

    public function isLoaded(string $name): bool;
}
