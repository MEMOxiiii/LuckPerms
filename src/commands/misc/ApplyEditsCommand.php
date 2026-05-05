<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\node\NodeEntry;
use jasonw4331\LuckPerms\webeditor\WebEditorResponse;
use pocketmine\command\CommandSender;
use Ramsey\Uuid\Uuid;
use pocketmine\utils\TextFormat;
use function is_array;
use function is_string;
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
                                }catch(\Throwable){
                                        // ignore invalid user ids in lightweight mode
                                }
                        }elseif($type === 'group'){
                                $name = isset($holder['id']) ? (string) $holder['id'] : (isset($holder['displayName']) ? (string) $holder['displayName'] : 'default');
                                $group = $plugin->getGroupManager()->getOrMake($name);
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
                        }
                }

                foreach($response->tracks() as $track){
			if(is_array($track) && isset($track['name'])){
				$plugin->getTrackManager()->getOrMake((string) $track['name']);
				$tracksApplied++;
			}
		}

		foreach($response->userDeletions() as $id){
			try{
				$plugin->getUserManager()->unload(Uuid::fromString($id));
				$usersDeleted++;
			}catch(\Throwable){
				// ignore invalid uuid
			}
		}
		foreach($response->groupDeletions() as $name){
			$plugin->getGroupManager()->delete($name);
			$groupsDeleted++;
		}
		foreach($response->trackDeletions() as $name){
			$plugin->getTrackManager()->delete($name);
			$tracksDeleted++;
		}

		$sender->sendMessage(TextFormat::GREEN . 'Applied web editor changes for code ' . $code);
		$sender->sendMessage(TextFormat::GRAY . 'Updated: users=' . $usersApplied . ', groups=' . $groupsApplied . ', tracks=' . $tracksApplied);
		$sender->sendMessage(TextFormat::GRAY . 'Deleted: users=' . $usersDeleted . ', groups=' . $groupsDeleted . ', tracks=' . $tracksDeleted);
	}

}
