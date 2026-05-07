<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use function array_keys;
use function array_map;
use function gzencode;
use function json_encode;
use function microtime;
use function sort;
use function sprintf;
use function strcmp;
use function usort;
use const JSON_THROW_ON_ERROR;

/**
 * Encapsulates a request payload to the LuckPerms web editor.
 * Mirrors WebEditorRequest.java from the LuckPerms common module.
 */
class WebEditorRequest{

	/** @param array<string, mixed> $payload */
	public function __construct(private array $payload){ }

	/** @return array<string, mixed> */
	public function getPayload() : array{
		return $this->payload;
	}

	/**
	 * GZIP-encodes the JSON payload for upload to bytebin.
	 */
	public function encode() : string{
		$json = json_encode($this->payload, JSON_THROW_ON_ERROR);
		$compressed = gzencode($json, 9);
		if($compressed === false){
			throw new \RuntimeException('Failed to GZIP-compress web editor payload');
		}
		return $compressed;
	}

	/**
	 * Generate a full web editor request payload that includes all loaded groups,
	 * online users (with their nodes), and all tracks.
	 */
	public static function generate(LuckPerms $plugin, CommandSender $sender, string $cmdLabel) : self{
		$permissionHolders = [];

		// All loaded groups (sorted by weight descending, then name)
		$groups = $plugin->getGroupManager()->getAll();
		usort($groups, static function(Group $a, Group $b) : int {
			$wDiff = $b->getWeight() - $a->getWeight();
			return $wDiff !== 0 ? $wDiff : strcmp($a->getName(), $b->getName());
		});
		foreach($groups as $group){
			$nodes = array_map(static fn(NodeEntry $n) => $n->toArray(), $group->getNodes());
			$permissionHolders[] = [
				'type' => 'group',
				'id' => $group->getName(),
				'displayName' => $group->getDisplayName() ?? $group->getName(),
				'nodes' => $nodes,
			];
		}

		// All stored users (from disk) — online players get their in-memory nodes
		// merged in so the most up-to-date data is always used.
		$onlinePlayers = [];
		foreach($plugin->getServer()->getOnlinePlayers() as $player){
			$onlinePlayers[$player->getUniqueId()->toString()] = $player;
		}

		$seenUuids = [];
		foreach($plugin->getStorage()->loadAllUsers() as $storedUser){
			$uuidStr = $storedUser->getUniqueId()->toString();
			$seenUuids[$uuidStr] = true;

			// If the player is online, prefer their current in-memory nodes
			if(isset($onlinePlayers[$uuidStr])){
				$onlinePlayer = $onlinePlayers[$uuidStr];
				$liveUser = $plugin->getUserManager()->load($onlinePlayer->getUniqueId(), $onlinePlayer->getName());
				$nodes = array_map(static fn(NodeEntry $n) => $n->toArray(), $liveUser->getNodes());
				$displayName = $onlinePlayer->getName();
			}else{
				$nodes = array_map(static fn(NodeEntry $n) => $n->toArray(), $storedUser->getNodes());
				$displayName = $storedUser->getUsername();
			}

			$permissionHolders[] = [
				'type' => 'user',
				'id' => $uuidStr,
				'displayName' => $displayName,
				'nodes' => $nodes,
			];
		}

		// Also include any online players who have no saved file yet
		foreach($onlinePlayers as $uuidStr => $player){
			if(isset($seenUuids[$uuidStr])) continue;
			$liveUser = $plugin->getUserManager()->load($player->getUniqueId(), $player->getName());
			$nodes = array_map(static fn(NodeEntry $n) => $n->toArray(), $liveUser->getNodes());
			$permissionHolders[] = [
				'type' => 'user',
				'id' => $uuidStr,
				'displayName' => $player->getName(),
				'nodes' => $nodes,
			];
		}

		// All tracks
		$tracksData = [];
		foreach($plugin->getTrackManager()->getAll() as $track){
			$tracksData[] = [
				'type' => 'track',
				'id' => $track->getName(),
				'groups' => $track->getGroups(),
			];
		}

		$uploaderUuid = $sender instanceof Player
			? $sender->getUniqueId()->toString()
			: '00000000-0000-0000-0000-000000000000';

		$payload = [
			'metadata' => [
				'commandAlias' => $cmdLabel,
				'uploader' => [
					'name' => $sender->getName(),
					'uuid' => $uploaderUuid,
				],
				'time' => (int) sprintf('%.0f', microtime(true) * 1000),
				'pluginVersion' => $plugin->getDescription()->getVersion(),
				'platform' => 'PocketMine-MP',
			],
			'permissionHolders' => $permissionHolders,
			'tracks' => $tracksData,
			'knownPermissions' => self::buildKnownPermissions(),
			'potentialContexts' => [],
		];

		return new self($payload);
	}

	/**
	 * Returns a sorted flat list of all permissions currently registered with PocketMine.
	 * Reading live at session-generation time ensures permissions from all plugins are included,
	 * regardless of plugin load order.
	 *
	 * @return string[]
	 */
	private static function buildKnownPermissions() : array{
		$names = array_keys(PermissionManager::getInstance()->getPermissions());
		sort($names);
		return $names;
	}
}
