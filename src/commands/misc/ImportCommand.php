<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Track;
use jasonw4331\LuckPerms\model\User;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use Ramsey\Uuid\Uuid;
use function file_exists;
use function file_get_contents;
use function is_array;
use function is_dir;
use function json_decode;
use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;

class ImportCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.import');
		$this->registerArgument(0, new RawStringArgument('file', false));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin   = LuckPerms::getInstance();
		$fileName = isset($args['file']) ? (string) $args['file'] : '';

		if($fileName === ''){
			$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' import <filename>');
			$sender->sendMessage(TF::GRAY . 'Files should be placed in: plugins/LuckPerms-master/exports/');
			return;
		}

		if(!str_ends_with($fileName, '.json')) $fileName .= '.json';

		$exportDir = $plugin->getDataFolder() . 'exports' . DIRECTORY_SEPARATOR;
		$path      = $exportDir . $fileName;

		if(!file_exists($path)){
			$sender->sendMessage(TF::RED . "File not found: exports/$fileName");
			return;
		}

		try{
			$data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
		}catch(\Throwable $e){
			$sender->sendMessage(TF::RED . 'Failed to parse file: ' . $e->getMessage());
			return;
		}

		if(!is_array($data)){
			$sender->sendMessage(TF::RED . 'Invalid export file format.');
			return;
		}

		$groupsImported = 0;
		$usersImported  = 0;
		$tracksImported = 0;

		// Import groups
		foreach($data['groups'] ?? [] as $groupData){
			if(!is_array($groupData)) continue;
			$name  = (string) ($groupData['name'] ?? '');
			if($name === '') continue;
			$group = $plugin->getGroupManager()->getOrMake($name);
			$nodes = [];
			foreach($groupData['nodes'] ?? [] as $rawNode){
				$entry = NodeEntry::fromArray($rawNode);
				if($entry !== null) $nodes[] = $entry;
			}
			$group->setNodes($nodes);
			$group->setWeight((int) ($groupData['weight'] ?? 0));
			$group->setDisplayName($groupData['displayName'] ?? null);
			$plugin->getStorage()->saveGroup($group);
			$groupsImported++;
		}

		// Import tracks
		foreach($data['tracks'] ?? [] as $trackData){
			if(!is_array($trackData)) continue;
			$name  = (string) ($trackData['name'] ?? '');
			if($name === '') continue;
			$track = $plugin->getTrackManager()->getOrMake($name);
			$track->setGroups($trackData['groups'] ?? []);
			$plugin->getStorage()->saveTrack($track);
			$tracksImported++;
		}

		// Import users
		foreach($data['users'] ?? [] as $userData){
			if(!is_array($userData)) continue;
			$uuidStr = (string) ($userData['uniqueId'] ?? '');
			$name    = (string) ($userData['username'] ?? 'Unknown');
			if($uuidStr === '') continue;
			try{
				$uuid = Uuid::fromString($uuidStr);
			}catch(\Throwable){
				continue;
			}
			$user  = $plugin->getUserManager()->load($uuid, $name);
			$nodes = [];
			foreach($userData['nodes'] ?? [] as $rawNode){
				$entry = NodeEntry::fromArray($rawNode);
				if($entry !== null) $nodes[] = $entry;
			}
			$user->setNodes($nodes);
			$plugin->getStorage()->saveUser($user);
			$usersImported++;
		}

		PermissionHelper::refreshAll($plugin);

		$sender->sendMessage(TF::GREEN . "Import complete from '$fileName'!");
		$sender->sendMessage(TF::GRAY . 'Groups: ' . $groupsImported . ' | Users: ' . $usersImported . ' | Tracks: ' . $tracksImported);
	}
}
