<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\calculator\processor;

use jasonw4331\LuckPerms\calculator\result\TristateResult;
use jasonw4331\LuckPerms\node\NodeEntry;
use function strtolower;

/**
 * Processor that handles direct permission matches
 */
class DirectProcessor implements PermissionProcessor {

	/** @var array<string, NodeEntry> */
	private array $sourceMap;

	/**
	 * @param array<string, NodeEntry> $sourceMap
	 */
	public function __construct(array $sourceMap) {
		$this->sourceMap = [];
		// Normalize all keys to lowercase
		foreach($sourceMap as $key => $node) {
			$this->sourceMap[strtolower($key)] = $node;
		}
	}

	public function hasPermission(TristateResult $prev, string $permission) : TristateResult {
		$permission = strtolower($permission);
		if(!isset($this->sourceMap[$permission])) {
			return TristateResult::UNDEFINED;
		}
		return TristateResult::fromBoolean($this->sourceMap[$permission]->getValue());
	}

	public function invalidate() : void {
		// No caching in processors
	}
}
