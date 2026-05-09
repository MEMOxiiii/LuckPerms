<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\calculator\processor;

use jasonw4331\LuckPerms\calculator\result\TristateResult;
use jasonw4331\LuckPerms\node\NodeEntry;
use function strtolower;
use function substr;
use function strrpos;

/**
 * Processor that handles wildcard permission matches
 */
class WildcardProcessor implements PermissionProcessor {

	private const WILDCARD_SUFFIX = '.*';
	private const ROOT_WILDCARD = '*';
	private const NODE_SEPARATOR = '.';

	/** @var array<string, TristateResult> */
	private array $wildcardPermissions = [];
	private TristateResult $rootWildcardState = TristateResult::UNDEFINED;

	/**
	 * @param array<string, NodeEntry> $sourceMap
	 */
	public function __construct(array $sourceMap) {
		// Process wildcard permissions
		foreach($sourceMap as $key => $node) {
			$key = strtolower($key);
			
			// Check for wildcard permissions
			if($this->isWildcardPermission($key)) {
				if($this->isRootWildcard($key)) {
					// Root wildcard: *
					$this->rootWildcardState = TristateResult::fromBoolean($node->getValue());
				} else {
					// Remove the .* suffix to get the parent key
					$parentKey = substr($key, 0, -2); // Remove '.*'
					$this->wildcardPermissions[$parentKey] = TristateResult::fromBoolean($node->getValue());
				}
			}
		}
	}

	public function hasPermission(TristateResult $prev, string $permission) : TristateResult {
		$permission = strtolower($permission);
		$node = $permission;

		// Check for matching wildcard at each level
		while(true) {
			$pos = strrpos($node, self::NODE_SEPARATOR);
			if($pos === false) {
				break;
			}

			$node = substr($node, 0, $pos);
			if($node !== '' && isset($this->wildcardPermissions[$node])) {
				$result = $this->wildcardPermissions[$node];
				if($result->isDefined()) {
					return $result;
				}
			}
		}

		// Check root wildcard
		return $this->rootWildcardState;
	}

	public function invalidate() : void {
		// No caching in processors
	}

	private function isRootWildcard(string $permission) : bool {
		return $permission === self::ROOT_WILDCARD;
	}

	private function isWildcardPermission(string $permission) : bool {
		return $this->isRootWildcard($permission) || 
			   (substr($permission, -2) === self::WILDCARD_SUFFIX && strlen($permission) > 2);
	}
}
