<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\permissible;

use jasonw4331\LuckPerms\calculator\CalculatorFactory;
use jasonw4331\LuckPerms\calculator\result\TristateResult;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\User;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use function str_starts_with;
use function strtolower;
use function substr;
use function time;

/**
 * Applies LuckPerms permission nodes to PocketMine players via PermissionAttachment.
 * 
 * This implementation matches the Java LuckPerms architecture:
 * - Uses a processor-based calculator system
 * - Properly handles permission inheritance through group hierarchies
 * - Implements wildcard permission support
 * - Caches permission calculations
 */
class PermissionHelper {
	/** @var array<string, PermissionAttachment> keyed by UUID string */
	private static array $attachments = [];

	/**
	 * Collect all permission nodes for a user, including inherited ones through groups.
	 * This builds a complete node map for the calculator.
	 *
	 * @return array<string, NodeEntry>
	 */
	public static function collectEffectiveNodes(User $user, LuckPerms $plugin, int $depth = 0) : array {
		if($depth > 50) return []; // prevent infinite recursion

		$now = time();
		/** @var array<string, NodeEntry> $nodes */
		$nodes = [];

		// First, add all direct nodes from the user
		foreach($user->getNodes() as $node) {
			// Skip expired temporary nodes
			if($node->isTemporary() && $node->getExpiry() !== null && $node->getExpiry() < $now) {
				continue;
			}

			$key = strtolower($node->getKey());

			// Handle group inheritance nodes
			if(str_starts_with($key, 'group.')) {
				$groupName = substr($key, 6);
				// Recursively get permissions from the group
				$groupNodes = self::collectGroupNodes($groupName, $plugin, $depth + 1, $now);
				// Merge group nodes (group nodes don't override user's direct permissions)
				foreach($groupNodes as $pKey => $pNode) {
					if(!isset($nodes[$pKey])) {
						$nodes[$pKey] = $pNode;
					}
				}
			} else {
				// Direct permission node
				$nodes[$key] = $node;
			}
		}

		return $nodes;
	}

	/**
	 * Collect all permission nodes from a group, including inherited parent groups.
	 *
	 * @return array<string, NodeEntry>
	 */
	private static function collectGroupNodes(string $groupName, LuckPerms $plugin, int $depth = 0, int $now = 0) : array {
		if($depth > 50) return [];
		if($now === 0) $now = time();

		$group = $plugin->getGroupManager()->getIfLoaded(strtolower($groupName));
		if($group === null) return [];

		/** @var array<string, NodeEntry> $nodes */
		$nodes = [];

		foreach($group->getNodes() as $node) {
			// Skip expired temporary nodes
			if($node->isTemporary() && $node->getExpiry() !== null && $node->getExpiry() < $now) {
				continue;
			}

			$key = strtolower($node->getKey());

			// Handle parent group inheritance
			if(str_starts_with($key, 'group.')) {
				$parentName = substr($key, 6);
				$parentNodes = self::collectGroupNodes($parentName, $plugin, $depth + 1, $now);
				// Merge parent nodes (parent doesn't override this group's permissions)
				foreach($parentNodes as $pKey => $pNode) {
					if(!isset($nodes[$pKey])) {
						$nodes[$pKey] = $pNode;
					}
				}
			} else {
				// Direct permission node from group
				if(!isset($nodes[$key])) {
					$nodes[$key] = $node;
				}
			}
		}

		return $nodes;
	}

	/**
	 * Apply (or refresh) permissions for an online player using the calculator system.
	 * 
	 * This method:
	 * 1. Collects all effective nodes (user + inherited from groups)
	 * 2. Creates a calculator with those nodes
	 * 3. Applies the calculated permissions to the player's attachment
	 */
	public static function applyPermissions(Player $player, User $user, LuckPerms $plugin) : void {
		// Remove old attachment if any
		self::clearPermissions($player, $plugin);

		// Collect all effective nodes
		$effectiveNodes = self::collectEffectiveNodes($user, $plugin);

		// Create a calculator for this player's permissions
		$calculator = CalculatorFactory::build($effectiveNodes);

		// Create a new attachment for this player
		$attachment = $player->addAttachment($plugin);
		self::$attachments[$player->getUniqueId()->toString()] = $attachment;

		// Get all unique permission nodes (not wildcards)
		$nonWildcardPerms = [];
		foreach($effectiveNodes as $key => $node) {
			// Skip wildcard nodes - they're handled by the calculator
			if(strpos($key, '*') === false) {
				$nonWildcardPerms[$key] = $node;
			}
		}

		// Apply non-wildcard permissions directly
		foreach($nonWildcardPerms as $perm => $node) {
			try {
				$attachment->setPermission($perm, $node->getValue());
			} catch(\Throwable $e) {
				// Log or ignore invalid permission names
			}
		}

		// Now we need to handle wildcard permissions
		// Create a list of "important" permissions that we need to calculate
		// This includes all permissions that might be checked by the server
		$allServerPermissions = self::getAllServerPermissions($plugin);

		// For each server permission, check if it should be granted via calculator
		foreach($allServerPermissions as $serverPerm) {
			$result = $calculator->checkPermission($serverPerm);
			if($result->isDefined()) {
				try {
					$attachment->setPermission($serverPerm, $result->toBoolean() ?? false);
				} catch(\Throwable $e) {
					// Ignore
				}
			}
		}

		// Store the calculator on the player for real-time checks
		// This allows hasPermission checks to use the calculator
		$player->setMetadata('luckperms_calculator', new \pocketmine\metadata\MetadataValue($plugin, $calculator));
	}

	/**
	 * Get all permissions registered on the server
	 */
	private static function getAllServerPermissions(LuckPerms $plugin) : array {
		// Get permissions from PocketMine's permission registry
		$perms = [];
		foreach($plugin->getServer()->getPermissionManager()->getPermissions() as $perm) {
			$perms[] = $perm->getName();
		}
		return $perms;
	}

	/**
	 * Legacy compatibility method: resolve effective permissions as boolean array.
	 * This is used by the "permission check" command and other admin utilities.
	 *
	 * @return array<string, bool>
	 */
	public static function resolveEffectivePermissions(User $user, LuckPerms $plugin) : array {
		$effectiveNodes = self::collectEffectiveNodes($user, $plugin);
		$result = [];
		foreach($effectiveNodes as $key => $node) {
			$result[$key] = $node->getValue();
		}
		return $result;
	}

	/**
	 * Legacy compatibility method: resolve group permissions as boolean array.
	 * This is used by verbose command and other admin utilities.
	 *
	 * @return array<string, bool>
	 */
	public static function resolveGroupPermissions(string $groupName, LuckPerms $plugin) : array {
		$groupNodes = self::collectGroupNodes($groupName, $plugin);
		$result = [];
		foreach($groupNodes as $key => $node) {
			$result[$key] = $node->getValue();
		}
		return $result;
	}

	/**
	 * Check if a player has a specific permission using the calculator.
	 * This is called during permission checks if the calculator is available.
	 */
	public static function checkPermission(Player $player, string $permission) : ?TristateResult {
		$metadata = $player->getMetadata('luckperms_calculator');
		if(empty($metadata)) {
			return null;
		}
		$calculator = $metadata[0]->getValue();
		return $calculator->checkPermission($permission);
	}

	/**
	 * Remove the LuckPerms permission attachment from a player.
	 */
	public static function clearPermissions(Player $player, LuckPerms $plugin) : void {
		$uuidStr = $player->getUniqueId()->toString();
		if(isset(self::$attachments[$uuidStr])) {
			try {
				$player->removeAttachment(self::$attachments[$uuidStr]);
			} catch(\Throwable) {}
			unset(self::$attachments[$uuidStr]);
		}
		// Also clear the metadata
		try {
			$player->removeMetadata('luckperms_calculator', $plugin);
		} catch(\Throwable) {}
	}

	/**
	 * Refresh permissions for all online players (call after group/user changes).
	 */
	public static function refreshAll(LuckPerms $plugin) : void {
		foreach($plugin->getServer()->getOnlinePlayers() as $player) {
			$user = $plugin->getUserManager()->getIfLoaded($player->getUniqueId());
			if($user !== null) {
				self::applyPermissions($player, $user, $plugin);
			}
		}
	}
}
