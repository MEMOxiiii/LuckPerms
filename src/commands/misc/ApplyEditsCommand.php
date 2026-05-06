<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\config\ConfigKeys;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\node\NodeEntry;
use jasonw4331\LuckPerms\webeditor\WebEditorRequest;
use jasonw4331\LuckPerms\webeditor\WebEditorResponse;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use Ramsey\Uuid\Uuid;
use function array_map;
use function array_values;
use function count;
use function is_array;
use function is_string;
use function rtrim;
use function strtolower;
use function trim;

class ApplyEditsCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.applyedits');
		$this->registerArgument(0, new RawStringArgument('code', false));
		$this->registerArgument(1, new RawStringArgument('target', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$code = isset($args['code']) ? trim((string) $args['code']) : '';
		if($code === ''){
			$sender->sendMessage(TextFormat::RED . 'Usage: /' . $aliasUsed . ' applyedits <code>');
			return;
		}

		$plugin = LuckPerms::getInstance();

		$sender->sendMessage(TextFormat::GOLD . 'Applying web editor session ' . TextFormat::WHITE . $code . TextFormat::GOLD . '...');

		try{
			$payload = $plugin->getBytebin()->getJsonContent($code);
		}catch(\Throwable $e){
			$sender->sendMessage(TextFormat::RED . 'Failed to download editor data: ' . $e->getMessage());
			return;
		}

		if(!is_array($payload)){
			$sender->sendMessage(TextFormat::RED . 'Invalid editor payload: expected JSON object');
			return;
		}
		$response = WebEditorResponse::fromArray($payload);

		$usersApplied = 0;
		$groupsApplied = 0;
		$tracksApplied = 0;
		$usersDeleted = 0;
		$groupsDeleted = 0;
		$tracksDeleted = 0;

		foreach($response->permissionHolders() as $holder){
			if(!is_array($holder)){
				continue;
			}
			$type = isset($holder['type']) && is_string($holder['type']) ? strtolower($holder['type']) : '';
			$rawNodes = isset($holder['nodes']) && is_array($holder['nodes']) ? $holder['nodes'] : [];

			if($type === 'user'){
				$id = isset($holder['id']) ? (string) $holder['id'] : '';
				$name = isset($holder['displayName']) ? (string) $holder['displayName'] : (isset($holder['name']) ? (string) $holder['name'] : $id);
				try{
					$uuid = Uuid::fromString($id);
					$user = $plugin->getUserManager()->load($uuid, $name);

					// Capture current nodes for diff
					$oldKeys = [];
					foreach($user->getNodes() as $n){
						$oldKeys[$n->getKey()] = $n->getValue();
					}

					$nodes = [];
					foreach($rawNodes as $rawNode){
						if(is_array($rawNode)){
							$entry = NodeEntry::fromArray($rawNode);
							if($entry !== null){
								$nodes[] = $entry;
							}
						}
					}
					$user->setNodes($nodes);
					$plugin->getStorage()->saveUser($user);
					$usersApplied++;

					// Build and show diff
					$newKeys = [];
					foreach($nodes as $n){
						$newKeys[$n->getKey()] = $n->getValue();
					}
					$this->sendDiff($sender, 'user', $name, $oldKeys, $newKeys);
				}catch(\Throwable){
					// ignore invalid user ids
				}
			}elseif($type === 'group'){
				$name = isset($holder['id']) ? (string) $holder['id'] : (isset($holder['displayName']) ? (string) $holder['displayName'] : 'default');
				$group = $plugin->getGroupManager()->getOrMake($name);

				// Capture current nodes for diff
				$oldKeys = [];
				foreach($group->getNodes() as $n){
					$oldKeys[$n->getKey()] = $n->getValue();
				}

				$nodes = [];
				foreach($rawNodes as $rawNode){
					if(is_array($rawNode)){
						$entry = NodeEntry::fromArray($rawNode);
						if($entry !== null){
							$nodes[] = $entry;
						}
					}
				}
				$group->setNodes($nodes);
				$plugin->getStorage()->saveGroup($group);
				$groupsApplied++;

				// Build and show diff
				$newKeys = [];
				foreach($nodes as $n){
					$newKeys[$n->getKey()] = $n->getValue();
				}
				$this->sendDiff($sender, 'group', $name, $oldKeys, $newKeys);
			}
		}

		foreach($response->tracks() as $track){
			if(!is_array($track)){
				continue;
			}
			$trackName = (is_string($track['id'] ?? null) ? $track['id'] : (is_string($track['name'] ?? null) ? $track['name'] : ''));
			if($trackName === '') continue;
			$trackObj = $plugin->getTrackManager()->getOrMake($trackName);
			if(isset($track['groups']) && is_array($track['groups'])){
				$trackObj->setGroups(array_values(array_map('strval', $track['groups'])));
			}
			$plugin->getStorage()->saveTrack($trackObj);
			$tracksApplied++;
			$sender->sendMessage(TextFormat::DARK_GRAY . '> ' . TextFormat::GRAY . 'Saved track ' . TextFormat::AQUA . $trackName);
		}

		foreach($response->userDeletions() as $id){
			try{
				$plugin->getUserManager()->unload(Uuid::fromString($id));
				$usersDeleted++;
				$sender->sendMessage(TextFormat::DARK_GRAY . '> ' . TextFormat::RED . 'Deleted user ' . TextFormat::WHITE . $id);
			}catch(\Throwable){
				// ignore invalid uuid
			}
		}
		foreach($response->groupDeletions() as $name){
			$plugin->getGroupManager()->delete($name);
			$groupsDeleted++;
			$sender->sendMessage(TextFormat::DARK_GRAY . '> ' . TextFormat::RED . 'Deleted group ' . TextFormat::WHITE . $name);
		}
		foreach($response->trackDeletions() as $name){
			$plugin->getTrackManager()->delete($name);
			$tracksDeleted++;
			$sender->sendMessage(TextFormat::DARK_GRAY . '> ' . TextFormat::RED . 'Deleted track ' . TextFormat::WHITE . $name);
		}

		$sender->sendMessage(TextFormat::GREEN . 'Successfully applied changes from editor session ' . TextFormat::WHITE . $code . TextFormat::GREEN . '.');
		$sender->sendMessage(
			TextFormat::GRAY . 'Updated: ' .
			TextFormat::WHITE . $usersApplied . TextFormat::GRAY . ' user' . ($usersApplied !== 1 ? 's' : '') . ', ' .
			TextFormat::WHITE . $groupsApplied . TextFormat::GRAY . ' group' . ($groupsApplied !== 1 ? 's' : '') . ', ' .
			TextFormat::WHITE . $tracksApplied . TextFormat::GRAY . ' track' . ($tracksApplied !== 1 ? 's' : '') . '.'
		);
		if($usersDeleted + $groupsDeleted + $tracksDeleted > 0){
			$sender->sendMessage(
				TextFormat::GRAY . 'Deleted: ' .
				TextFormat::WHITE . $usersDeleted . TextFormat::GRAY . ' user' . ($usersDeleted !== 1 ? 's' : '') . ', ' .
				TextFormat::WHITE . $groupsDeleted . TextFormat::GRAY . ' group' . ($groupsDeleted !== 1 ? 's' : '') . ', ' .
				TextFormat::WHITE . $tracksDeleted . TextFormat::GRAY . ' track' . ($tracksDeleted !== 1 ? 's' : '') . '.'
			);
		}

		// PATCH the original bytebin entry in-place so the same URL reflects
		// the updated data - the editor session key/URL stays identical.
		try{
			$request = WebEditorRequest::generate($plugin, $sender, $aliasUsed);
			$plugin->getBytebin()->patchContent($code, $request->encode(), 'application/json; charset=utf-8', 'editor');
			$sender->sendMessage(TextFormat::GOLD . 'Editor session updated. You can refresh the same editor page to continue editing.');
		}catch(\Throwable){
			// Bytebin may not support PATCH â€” fall back to posting a new session
			try{
				$request = WebEditorRequest::generate($plugin, $sender, $aliasUsed);
				$content = $plugin->getBytebin()->postContent($request->encode(), 'application/json; charset=utf-8', 'editor');
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
				$sender->sendMessage(TextFormat::GOLD . 'Updated editor session: ' . TextFormat::AQUA . $url);
			}catch(\Throwable){
				// Non-fatal: changes were saved, session refresh unavailable
			}
		}
	}

	/**
	 * Sends added/removed permission diff lines for a holder.
	 *
	 * @param array<string, bool> $oldKeys key => value before
	 * @param array<string, bool> $newKeys key => value after
	 */
	private function sendDiff(CommandSender $sender, string $type, string $name, array $oldKeys, array $newKeys) : void{
		$added = [];
		$removed = [];

		foreach($newKeys as $key => $value){
			if(!isset($oldKeys[$key])){
				$added[] = $key . ': ' . ($value ? 'true' : 'false');
			}
		}
		foreach($oldKeys as $key => $value){
			if(!isset($newKeys[$key])){
				$removed[] = $key . ': ' . ($value ? 'true' : 'false');
			}
		}

		$total = count($newKeys);
		$addedCount = count($added);
		$removedCount = count($removed);

		$sender->sendMessage(
			TextFormat::DARK_GRAY . '> ' . TextFormat::GRAY . 'Saved ' . $type . ' ' . TextFormat::AQUA . $name .
			TextFormat::GRAY . ' (' . $total . ' node' . ($total !== 1 ? 's' : '') .
			($addedCount > 0 ? TextFormat::GREEN . ' +' . $addedCount : '') .
			($removedCount > 0 ? TextFormat::RED . ' -' . $removedCount : '') .
			TextFormat::GRAY . ')'
		);
		foreach($added as $entry){
			$sender->sendMessage(TextFormat::DARK_GRAY . '   ' . TextFormat::GREEN . '+ ' . $entry);
		}
		foreach($removed as $entry){
			$sender->sendMessage(TextFormat::DARK_GRAY . '   ' . TextFormat::RED . '- ' . $entry);
		}
	}

}
