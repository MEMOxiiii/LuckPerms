<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function array_map;
use function count;
use function gzencode;
use function json_encode;
use function microtime;
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

		// Online users with their actual stored nodes
		foreach($plugin->getServer()->getOnlinePlayers() as $player){
			$uuid = $player->getUniqueId();
			$user = $plugin->getUserManager()->load($uuid, $player->getName());
			// Try to load from storage if user has no nodes yet
			if(count($user->getNodes()) === 0){
				$loaded = $plugin->getStorage()->loadUser($uuid);
				if($loaded !== null){
					$user->setNodes($loaded->getNodes());
				}
			}
			$nodes = array_map(static fn(NodeEntry $n) => $n->toArray(), $user->getNodes());
			$permissionHolders[] = [
				'type' => 'user',
				'id' => $uuid->toString(),
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
			'knownPermissions' => $plugin->getPermissionRegistry()->rootAsList(),
			'potentialContexts' => [],
		];

		return new self($payload);
	}
}
