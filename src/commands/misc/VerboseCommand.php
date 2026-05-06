<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_filter;
use function array_map;
use function array_slice;
use function count;
use function implode;
use function strtolower;
use function substr;
use function str_starts_with;

class VerboseCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.verbose');
		$this->registerArgument(0, new RawStringArgument('target', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$target = isset($args['target']) ? (string) $args['target'] : '';
		$plugin = LuckPerms::getInstance();

		if($target === ''){
			$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' verbose <player|group:<name>>');
			$sender->sendMessage(TF::GRAY . 'Shows the resolved effective permissions for a player or group.');
			return;
		}

		// Resolve group
		if(str_starts_with(strtolower($target), 'group:')){
			$groupName = substr($target, 6);
			$group     = $plugin->getGroupManager()->getIfLoaded(strtolower($groupName));
			if($group === null){
				$sender->sendMessage(TF::RED . "Group '$groupName' is not loaded.");
				return;
			}
			$perms = PermissionHelper::resolveGroupPermissions($groupName, $plugin);
			$sender->sendMessage(TF::GOLD . '=== Effective permissions for group: ' . TF::WHITE . $groupName . TF::GOLD . ' (' . count($perms) . ' nodes) ===');
			$lines = [];
			foreach($perms as $key => $val){
				$lines[] = ($val ? TF::GREEN : TF::RED) . $key;
			}
			foreach(array_slice($lines, 0, 60) as $line) $sender->sendMessage('  ' . $line);
			if(count($lines) > 60) $sender->sendMessage(TF::GRAY . '  ... and ' . (count($lines) - 60) . ' more.');
			return;
		}

		// Resolve player
		$player = $plugin->getServer()->getPlayerByPrefix($target);
		if($player === null){
			$sender->sendMessage(TF::RED . "Player '$target' is not online.");
			return;
		}
		$user = $plugin->getUserManager()->getIfLoaded($player->getUniqueId());
		if($user === null){
			$sender->sendMessage(TF::RED . 'User data for ' . $player->getName() . ' is not loaded.');
			return;
		}
		$perms = PermissionHelper::resolveEffectivePermissions($user, $plugin);

		// also show which groups they're in
		$groups = array_map(
			static fn($n) => substr($n->getKey(), 6),
			array_filter($user->getNodes(), static fn($n) => str_starts_with(strtolower($n->getKey()), 'group.'))
		);

		$sender->sendMessage(TF::GOLD . '=== Effective permissions for: ' . TF::WHITE . $player->getName() . TF::GOLD . ' (' . count($perms) . ' nodes) ===');
		if(!empty($groups)){
			$sender->sendMessage(TF::YELLOW . 'Groups: ' . TF::WHITE . implode(', ', $groups));
		}
		$lines = [];
		foreach($perms as $key => $val){
			$lines[] = ($val ? TF::GREEN : TF::RED) . $key;
		}
		foreach(array_slice($lines, 0, 60) as $line) $sender->sendMessage('  ' . $line);
		if(count($lines) > 60) $sender->sendMessage(TF::GRAY . '  ... and ' . (count($lines) - 60) . ' more.');
	}
}
