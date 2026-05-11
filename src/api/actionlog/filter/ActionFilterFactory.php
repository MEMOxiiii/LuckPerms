<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog\filter;

use Ramsey\Uuid\UuidInterface;

/**
 * Creates ActionFilter instances.
 */
interface ActionFilterFactory
{
    public function any(): ActionFilter;

    public function source(UuidInterface $uniqueId): ActionFilter;

    public function user(UuidInterface $uniqueId): ActionFilter;

    public function group(string $name): ActionFilter;

    public function track(string $name): ActionFilter;

    public function search(string $query): ActionFilter;
}
