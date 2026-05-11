<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\sender\Sender;
use jasonw4331\LuckPerms\verbose\VerboseFilter;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_filter;
use function array_map;
use function array_slice;
use function count;
use function implode;
use function is_string;
use function str_starts_with;
use function strtolower;
use function substr;
use function trim;

class VerboseCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.verbose');
		$this->registerArgument(0, new RawStringArgument('args', true));
	}

	/** @param array<mixed> $args */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$input = isset($args['args']) && is_string($args['args']) ? trim($args['args']) : '';
		$plugin = LuckPerms::getInstance();

		if($input === ''){
			$sender->sendMessage(TF::GOLD . 'Verbose recording:');
			$sender->sendMessage(TF::WHITE . '  /' . $aliasUsed . ' verbose on [filter]' . TF::GRAY . ' - Start recording');
			$sender->sendMessage(TF::WHITE . '  /' . $aliasUsed . ' verbose off' . TF::GRAY . ' - Stop recording');
			$sender->sendMessage(TF::WHITE . '  /' . $aliasUsed . ' verbose <player|*|group:<name>>' . TF::GRAY . ' - View snapshot');
			return;
		}

		// Parse first word as sub-action
		$parts = explode(' ', $input, 2);
		$action = strtolower($parts[0]);
		$filterStr = $parts[1] ?? '';

		// --- ON: start verbose listener ---
		if($action === 'on' || $action === 'record'){
			$lpSender = $plugin->getSenderFactory()->wrap($sender);
			$filter = VerboseFilter::compile($filterStr);
			$plugin->getVerboseHandler()->registerListener($lpSender, $filter, true);
			$sender->sendMessage(TF::GREEN . 'Verbose recording started' . ($filterStr !== '' ? ' with filter: ' . TF::WHITE . $filterStr : '') . TF::GREEN . '.');
			$sender->sendMessage(TF::GRAY . 'Permission checks will be shown here in real-time. Use ' . TF::WHITE . '/' . $aliasUsed . ' verbose off' . TF::GRAY . ' to stop.');
			return;
		}

		// --- OFF: stop verbose listener ---
		if($action === 'off' || $action === 'stop'){
			$lpSender = $plugin->getSenderFactory()->wrap($sender);
			$listener = $plugin->getVerboseHandler()->unregisterListener($lpSender);
			if($listener === null){
				$sender->sendMessage(TF::RED . 'You do not have an active verbose listener.');
			} else {
				$sender->sendMessage(TF::GREEN . 'Verbose recording stopped. ' . TF::WHITE . $listener->getMatchedCounter() . TF::GREEN . ' checks matched out of ' . TF::WHITE . $listener->getCounter() . TF::GREEN . ' total.');
			}
			return;
		}

		// --- UPLOAD: upload recorded data ---
		if($action === 'upload' || $action === 'paste'){
			$sender->sendMessage(TF::YELLOW . 'Upload is not yet supported in this build.');
			return;
		}

		// --- SNAPSHOT: show effective permissions for player/group/* ---
		$target = $input;

		// Wildcard: show all online players
		if($target === '*'){
			$players = $plugin->getServer()->getOnlinePlayers();
			if(count($players) === 0){
				$sender->sendMessage(TF::RED . 'No players are currently online.');
				return;
			}
			foreach($players as $player){
				$user = $plugin->getUserManager()->getIfLoaded($player->getUniqueId());
				if($user === null) continue;
				$perms = PermissionHelper::resolveEffectivePermissions($user, $plugin);
				$groups = array_map(
					static fn($n) => substr($n->getKey(), 6),
					array_filter($user->getNodes(), static fn($n) => str_starts_with(strtolower($n->getKey()), 'group.'))
				);
				$sender->sendMessage(TF::GOLD . '--- ' . $player->getName() . TF::GOLD . ' (' . count($perms) . ' nodes, groups: ' . TF::WHITE . implode(', ', $groups) . TF::GOLD . ') ---');
				$lines = [];
				foreach($perms as $key => $val){
					$lines[] = ($val ? TF::GREEN : TF::RED) . $key;
				}
				foreach(array_slice($lines, 0, 20) as $line) $sender->sendMessage('  ' . $line);
				if(count($lines) > 20) $sender->sendMessage(TF::GRAY . '  ... and ' . (count($lines) - 20) . ' more.');
			}
			return;
		}

		// Group snapshot
		if(str_starts_with(strtolower($target), 'group:')){
			$groupName = substr($target, 6);
			$group = $plugin->getGroupManager()->getIfLoaded(strtolower($groupName));
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

		// Player snapshot
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

		$groups = array_map(
			static fn($n) => substr($n->getKey(), 6),
			array_filter($user->getNodes(), static fn($n) => str_starts_with(strtolower($n->getKey()), 'group.'))
		);

		$sender->sendMessage(TF::GOLD . '=== Effective permissions for: ' . TF::WHITE . $player->getName() . TF::GOLD . ' (' . count($perms) . ' nodes) ===');
		if(count($groups) > 0){
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
