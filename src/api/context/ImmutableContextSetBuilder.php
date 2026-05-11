<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Builder for ImmutableContextSet instances.
 */
interface ImmutableContextSetBuilder
{
	public function add(string $key, string $value) : self;

	/**
	 * @param iterable<array{string, string}> $iterable
	 */
	public function addAll(iterable $iterable) : self;

	public function addAllFromContextSet(ContextSet $contextSet) : self;

	public function build() : ImmutableContextSet;
}
