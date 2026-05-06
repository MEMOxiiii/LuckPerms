<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\permissible;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\User;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use function str_starts_with;
use function strtolower;
use function substr;
use function time;

/**
 * Applies LuckPerms permission nodes to PocketMine players via PermissionAttachment.
 */
class PermissionHelper{
	/** @var array<string, PermissionAttachment> keyed by UUID string */
	private static array $attachments = [];

	/**
	 * Resolve all effective permission nodes for a user (own + inherited groups, recursively).
	 *
	 * @return array<string, bool>  permission key => value
	 */
	public static function resolveEffectivePermissions(User $user, LuckPerms $plugin, int $depth = 0) : array{
		if($depth > 20) return []; // prevent infinite recursion

		$now = time();
		/** @var array<string, bool> $permissions */
		$permissions = [];

		foreach($user->getNodes() as $node){
			// skip expired temporary nodes
			if($node->isTemporary() && $node->getExpiry() !== null && $node->getExpiry() < $now){
				continue;
			}
			$key = strtolower($node->getKey());
			// group inheritance node: "group.<name>"
			if(str_starts_with($key, 'group.')){
				$groupName = substr($key, 6);
				$group = $plugin->getGroupManager()->getIfLoaded($groupName);
				if($group !== null){
					$groupPerms = self::resolveGroupPermissions($groupName, $plugin, $depth + 1);
					foreach($groupPerms as $perm => $val){
						$permissions[$perm] ??= $val;
					}
				}
			}else{
				$permissions[$key] = $node->getValue();
			}
		}
		return $permissions;
	}

	/**
	 * @return array<string, bool>
	 */
	public static function resolveGroupPermissions(string $groupName, LuckPerms $plugin, int $depth = 0) : array{
		if($depth > 20) return [];

		$now = time();
		$group = $plugin->getGroupManager()->getIfLoaded($groupName);
		if($group === null) return [];

		/** @var array<string, bool> $permissions */
		$permissions = [];
		foreach($group->getNodes() as $node){
			if($node->isTemporary() && $node->getExpiry() !== null && $node->getExpiry() < $now){
				continue;
			}
			$key = strtolower($node->getKey());
			if(str_starts_with($key, 'group.')){
				$parentName = substr($key, 6);
				$inherited = self::resolveGroupPermissions($parentName, $plugin, $depth + 1);
				foreach($inherited as $perm => $val){
					$permissions[$perm] ??= $val;
				}
			}else{
				$permissions[$key] = $node->getValue();
			}
		}
		return $permissions;
	}

	/**
	 * Apply (or refresh) permissions for an online player.
	 */
	public static function applyPermissions(Player $player, User $user, LuckPerms $plugin) : void{
		// Remove old attachment if any
		self::clearPermissions($player, $plugin);

		$attachment = $player->addAttachment($plugin);
		self::$attachments[$player->getUniqueId()->toString()] = $attachment;

		$effective = self::resolveEffectivePermissions($user, $plugin);
		foreach($effective as $perm => $value){
			try{
				$attachment->setPermission($perm, $value);
			}catch(\Throwable){
				// ignore invalid permission names
			}
		}
	}

	/**
	 * Remove the LuckPerms permission attachment from a player.
	 */
	public static function clearPermissions(Player $player, LuckPerms $plugin) : void{
		$uuidStr = $player->getUniqueId()->toString();
		if(isset(self::$attachments[$uuidStr])){
			try{
				$player->removeAttachment(self::$attachments[$uuidStr]);
			}catch(\Throwable){}
			unset(self::$attachments[$uuidStr]);
		}
	}

	/**
	 * Refresh permissions for all online players (call after group/user changes).
	 */
	public static function refreshAll(LuckPerms $plugin) : void{
		foreach($plugin->getServer()->getOnlinePlayers() as $player){
			$user = $plugin->getUserManager()->getIfLoaded($player->getUniqueId());
			if($user !== null){
				self::applyPermissions($player, $user, $plugin);
			}
		}
	}
}
