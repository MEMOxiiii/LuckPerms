<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\calculator\result;

/**
 * Represents a tristate permission result: True, False, or Undefined
 */
enum TristateResult : string {
	case TRUE = 'true';
	case FALSE = 'false';
	case UNDEFINED = 'undefined';

	/**
	 * Get the boolean value, or null if undefined
	 */
	public function toBoolean() : ?bool {
		return match($this) {
			TristateResult::TRUE => true,
			TristateResult::FALSE => false,
			TristateResult::UNDEFINED => null,
		};
	}

	/**
	 * Check if this result is defined (true or false)
	 */
	public function isDefined() : bool {
		return $this !== TristateResult::UNDEFINED;
	}

	/**
	 * Create from a boolean value
	 */
	public static function fromBoolean(?bool $value) : self {
		return match($value) {
			true => TristateResult::TRUE,
			false => TristateResult::FALSE,
			null => TristateResult::UNDEFINED,
		};
	}
}
