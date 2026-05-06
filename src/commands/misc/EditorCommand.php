<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\config\ConfigKeys;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_map;
use function gzencode;
use function json_encode;
use function microtime;
use function rtrim;
use function sprintf;
use const JSON_THROW_ON_ERROR;

class EditorCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.editor');
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin = LuckPerms::getInstance();

		try{
			$permissionHolders = [];

			// All loaded groups with their actual nodes
			foreach($plugin->getGroupManager()->getAll() as $group){
				$nodes = array_map(static fn(NodeEntry $n) => $n->toArray(), $group->getNodes());
				$permissionHolders[] = [
					'type'        => 'group',
					'id'          => $group->getName(),
					'displayName' => $group->getDisplayName() ?? $group->getName(),
					'nodes'       => $nodes,
				];
			}

			// All online players with their actual nodes
			foreach($plugin->getServer()->getOnlinePlayers() as $player){
				$uuid = $player->getUniqueId();
				$user = $plugin->getUserManager()->load($uuid, $player->getName());
				$nodes = array_map(static fn(NodeEntry $n) => $n->toArray(), $user->getNodes());
				$permissionHolders[] = [
					'type'        => 'user',
					'id'          => $uuid->toString(),
					'displayName' => $player->getName(),
					'nodes'       => $nodes,
				];
			}

			// All tracks with their group lists
			$tracksData = [];
			foreach($plugin->getTrackManager()->getAll() as $track){
				$tracksData[] = [
					'type'   => 'track',
					'id'     => $track->getName(),
					'groups' => $track->getGroups(),
				];
			}

			$uploaderUuid = $sender instanceof Player ? $sender->getUniqueId()->toString() : '00000000-0000-0000-0000-000000000000';
			$payload = json_encode([
				'metadata' => [
					'commandAlias' => $aliasUsed,
					'uploader'     => [
						'name' => $sender->getName(),
						'uuid' => $uploaderUuid,
					],
					'time'          => (int) sprintf('%.0f', microtime(true) * 1000),
					'pluginVersion' => $plugin->getDescription()->getVersion(),
					'platform'      => 'PocketMine-MP',
				],
				'permissionHolders' => $permissionHolders,
				'tracks'            => $tracksData,
				'knownPermissions'  => $plugin->getPermissionRegistry()->rootAsList(),
				'potentialContexts' => [],
			], JSON_THROW_ON_ERROR);

			$compressed = gzencode($payload, 9);
			if($compressed === false){
				$sender->sendMessage(TextFormat::RED . 'Failed to compress editor payload.');
				return;
			}

			$content = $plugin->getBytebin()->postContent($compressed, 'application/json; charset=utf-8', 'editor');
			$base = 'https://luckperms.net/editor/';
			try{
				$configured = (string) $plugin->getConfiguration()->get(ConfigKeys::WEB_EDITOR_URL_PATTERN());
				if($configured !== ''){
					$base = $configured;
				}
			}catch(\Throwable){
				// keep official default URL
			}
			$url = rtrim($base, '/') . '/' . $content->getKey();

			$sender->sendMessage(TextFormat::GREEN . 'Web editor URL: ' . TextFormat::AQUA . $url);
			if(count($permissionHolders) === 0){
				$sender->sendMessage(TextFormat::YELLOW . 'Note: No groups or online players loaded. Create groups with /lp creategroup <name>.');
			}
		}catch(\Throwable $e){
			$sender->sendMessage(TextFormat::RED . 'Failed to create web editor session: ' . $e->getMessage());
		}
	}

}

