<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\config\ConfigKeys;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
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
			foreach($plugin->getServer()->getOnlinePlayers() as $player){
				$permissionHolders[] = [
					'type' => 'user',
					'id' => $player->getUniqueId()->toString(),
					'displayName' => $player->getName(),
					'nodes' => [],
				];
			}

			$uploaderUuid = $sender instanceof Player ? $sender->getUniqueId()->toString() : '00000000-0000-0000-0000-000000000000';
			$payload = json_encode([
				'metadata' => [
					'commandAlias' => $aliasUsed,
					'uploader' => [
						'name' => $sender->getName(),
						'uuid' => $uploaderUuid,
					],
					'time' => (int) sprintf('%.0f', microtime(true) * 1000),
					'pluginVersion' => $plugin->getDescription()->getVersion(),
					'platform' => 'PocketMine-MP',
				],
				'permissionHolders' => $permissionHolders,
				'tracks' => [],
				'knownPermissions' => $plugin->getPermissionRegistry()->rootAsList(),
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
		}catch(\Throwable $e){
			$sender->sendMessage(TextFormat::RED . 'Failed to create web editor session: ' . $e->getMessage());
		}
	}

}
