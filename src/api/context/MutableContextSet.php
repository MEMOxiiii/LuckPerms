<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * A mutable implementation of ContextSet.
 */
interface MutableContextSet extends ContextSet
{
    public static function create(): self;

    public static function of(string $key, string $value): self;

    public function add(string $key, string $value): void;

    /**
     * @param iterable<array{string, string}>|ContextSet $iterable
     */
    public function addAll(iterable|ContextSet $iterable): void;

    public function remove(string $key, string $value): void;

    public function removeAll(string $key): void;

    public function clear(): void;
}
