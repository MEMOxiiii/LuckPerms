<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Provides factory methods for creating ContextSet instances.
 */
interface ContextSetFactory
{
	public function immutableBuilder() : ImmutableContextSetBuilder;

	public function immutableOf(string $key, string $value) : ImmutableContextSet;

	public function immutableEmpty() : ImmutableContextSet;

	public function mutable() : MutableContextSet;
}
