<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_map;
use function array_values;
use function count;
use function date;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_string;
use function json_decode;
use function json_encode;
use function mkdir;
use function scandir;
use function str_ends_with;
use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

class ExportCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.export');
		$this->registerArgument(0, new RawStringArgument('file', true));
	}

	/** @param array<mixed> $args */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin = LuckPerms::getInstance();
		$fileName = isset($args['file']) && is_string($args['file']) ? $args['file'] : ('export-' . date('Y-m-d_H-i-s') . '.json');

		// Ensure .json extension
		if(!str_ends_with($fileName, '.json')) $fileName .= '.json';

		$exportDir = $plugin->getDataFolder() . 'exports' . DIRECTORY_SEPARATOR;
		if(!is_dir($exportDir)) mkdir($exportDir, 0777, true);

		$data = [
			'metadata' => [
				'generatedBy' => 'LuckPerms-PocketMine',
				'generatedAt' => date('c'),
			],
			'groups' => [],
			'users' => [],
			'tracks' => [],
		];

		foreach($plugin->getGroupManager()->getAll() as $group){
			$data['groups'][] = [
				'name' => $group->getName(),
				'weight' => $group->getWeight(),
				'displayName' => $group->getDisplayName(),
				'nodes' => array_values(array_map(static fn(NodeEntry $n) => [
					'key' => $n->getKey(),
					'value' => $n->getValue(),
					'context' => $n->getContext(),
					'expiry' => $n->getExpiry(),
				], $group->getNodes())),
			];
		}

		foreach($plugin->getTrackManager()->getAll() as $track){
			$data['tracks'][] = [
				'name' => $track->getName(),
				'groups' => $track->getGroups(),
			];
		}

		// Export all user JSON files (includes offline players)
		$userDir = $plugin->getDataFolder() . 'users' . DIRECTORY_SEPARATOR;
		if(is_dir($userDir)){
			$scanResult = scandir($userDir);
			foreach($scanResult !== false ? $scanResult : [] as $file){
				if(!str_ends_with($file, '.json')) continue;
				try{
					$content = file_get_contents($userDir . $file);
					if($content === false) continue;
					$userData = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
					$data['users'][] = $userData;
				}catch(\Throwable){}
			}
		}

		$path = $exportDir . $fileName;
		file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

		$sender->sendMessage(TF::GREEN . 'Export complete! File saved to: ' . TF::WHITE . 'plugins/LuckPerms-master/exports/' . $fileName);
		$sender->sendMessage(TF::GRAY . 'Groups: ' . count($data['groups']) . ' | Users: ' . count($data['users']) . ' | Tracks: ' . count($data['tracks']));
	}
}
