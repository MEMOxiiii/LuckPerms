<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query;

/**
 * Represents a key for a custom option defined in QueryOptions.
 */
interface OptionKey
{
	/**
	 * Creates a new OptionKey for the given name and type.
	 */
	public static function of(string $name, string $type) : self;

	/**
	 * Gets a name describing the key type.
	 */
	public function name() : string;

	/**
	 * Gets the type class name of the key.
	 */
	public function type() : string;
}
