<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog\filter;

use jasonw4331\LuckPerms\api\actionlog\Action;
use Ramsey\Uuid\UuidInterface;

/**
 * Represents a filter for Action queries.
 */
interface ActionFilter
{
    public function test(Action $action): bool;

    public static function any(): self;

    public static function source(UuidInterface $uniqueId): self;

    public static function user(UuidInterface $uniqueId): self;

    public static function group(string $name): self;

    public static function track(string $name): self;

    public static function search(string $query): self;
}
