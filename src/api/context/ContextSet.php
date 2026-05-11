<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Represents a set of context pairs.
 */
interface ContextSet
{
	public function isImmutable() : bool;

	public function immutableCopy() : ImmutableContextSet;

	public function mutableCopy() : MutableContextSet;

	/**
	 * @return array<string, string[]>
	 */
	public function toSet() : array;

	/**
	 * @return array<string, string[]>
	 */
	public function toMap() : array;

	/**
	 * @deprecated
	 * @return array<string, string>
	 */
	public function toFlattenedMap() : array;

	public function containsKey(string $key) : bool;

	/**
	 * @return string[]
	 */
	public function getValues(string $key) : array;

	public function getAnyValue(string $key) : ?string;

	public function contains(string $key, string $value) : bool;

	public function isEmpty() : bool;

	public function size() : int;
}
