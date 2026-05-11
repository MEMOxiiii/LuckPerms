<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * An immutable implementation of ContextSet.
 */
interface ImmutableContextSet extends ContextSet
{
	public static function builder() : ImmutableContextSetBuilder;

	public static function empty() : self;

	public static function of(string $key, string $value) : self;
}
