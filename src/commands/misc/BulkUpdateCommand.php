<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\model\User;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_values;
use function in_array;
use function is_string;
use function str_ends_with;
use function str_starts_with;
use function strtolower;
use function substr;

/**
 * /lp bulkupdate <type> <action> <node> [replacement]
 *
 * type        : all | user | group
 * action      : delete | replace
 * node        : the permission node to target (supports * wildcard at end)
 * replacement : (required for replace) the new node name
 *
 * Examples:
 *   /lp bulkupdate all delete example.old
 *   /lp bulkupdate group replace old.perm new.perm
 */
class BulkUpdateCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.command');
		$this->registerArgument(0, new RawStringArgument('type', false));
		$this->registerArgument(1, new RawStringArgument('action', false));
		$this->registerArgument(2, new RawStringArgument('node', false));
		$this->registerArgument(3, new RawStringArgument('replacement', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$type = isset($args['type']) && is_string($args['type']) ? strtolower((string) $args['type']) : '';
		$action = isset($args['action']) && is_string($args['action']) ? strtolower((string) $args['action']) : '';
		$node = isset($args['node']) && is_string($args['node']) ? strtolower((string) $args['node']) : '';
		$replacement = isset($args['replacement']) && is_string($args['replacement']) ? (string) $args['replacement'] : '';

		if($type === '' || $action === '' || $node === ''){
			$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' bulkupdate <all|user|group> <delete|replace> <node> [replacement]');
			return;
		}
		if(!in_array($type, ['all', 'user', 'group'], true)){
			$sender->sendMessage(TF::RED . "Unknown type '$type'. Use: all, user, group");
			return;
		}
		if(!in_array($action, ['delete', 'replace'], true)){
			$sender->sendMessage(TF::RED . "Unknown action '$action'. Use: delete, replace");
			return;
		}
		if($action === 'replace' && $replacement === ''){
			$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' bulkupdate ' . $type . ' replace <node> <replacement>');
			return;
		}

		$plugin = LuckPerms::getInstance();
		$changed = 0;

		$nodeFilter = static function(string $key) use ($node) : bool{
			if(str_ends_with($node, '*')){
				return str_starts_with($key, substr($node, 0, -1));
			}
			return $key === $node;
		};

		if($type === 'all' || $type === 'user'){
			foreach($plugin->getUserManager()->getAll() as $user){
				if($this->processHolder($user, $action, $nodeFilter, $replacement)){
					$plugin->getStorage()->saveUser($user);
					$changed++;
				}
			}
		}

		if($type === 'all' || $type === 'group'){
			foreach($plugin->getGroupManager()->getAll() as $group){
				if($this->processHolder($group, $action, $nodeFilter, $replacement)){
					$plugin->getStorage()->saveGroup($group);
					$changed++;
				}
			}
		}

		PermissionHelper::refreshAll($plugin);
		$actionDesc = $action === 'delete' ? "deleted '$node'" : "replaced '$node' → '$replacement'";
		$sender->sendMessage(TF::GREEN . "Bulk update complete: $actionDesc on $changed holder(s).");
	}

	/**
	 * @param User|Group             $holder
	 * @param \Closure(string): bool $filter
	 */
	private function processHolder(object $holder, string $action, \Closure $filter, string $replacement) : bool{
		$nodes = $holder->getNodes();
		$modified = false;
		$newNodes = [];
		foreach($nodes as $node){
			$key = strtolower($node->getKey());
			if($filter($key)){
				$modified = true;
				if($action === 'replace' && $replacement !== ''){
					$newNodes[] = new NodeEntry($replacement, $node->getValue(), $node->getContext(), $node->getExpiry());
				}
				// delete → skip adding
			}else{
				$newNodes[] = $node;
			}
		}
		if($modified) $holder->setNodes(array_values($newNodes));
		return $modified;
	}
}
