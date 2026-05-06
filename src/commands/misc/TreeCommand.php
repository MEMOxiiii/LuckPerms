<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat as TF;
use function array_filter;
use function array_slice;
use function count;
use function is_string;
use function ksort;
use function str_starts_with;

class TreeCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.command');
		$this->registerArgument(0, new RawStringArgument('scope', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$scope = isset($args['scope']) && is_string($args['scope']) ? (string) $args['scope'] : '.';

		$all = PermissionManager::getInstance()->getPermissions();
		if($scope !== '.'){
			$all = array_filter($all, static fn(\pocketmine\permission\Permission $p) => str_starts_with($p->getName(), $scope));
		}
		ksort($all);

		if(count($all) === 0){
			$sender->sendMessage(TF::YELLOW . 'No permissions found' . ($scope !== '.' ? ' matching: ' . $scope : '') . '.');
			return;
		}

		$sender->sendMessage(TF::GOLD . '--- Permission Tree' . ($scope !== '.' ? ' [' . $scope . ']' : '') . ' (' . count($all) . ') ---');
		$shown = array_slice($all, 0, 50);
		foreach($shown as $perm){
			$sender->sendMessage(TF::YELLOW . '  ' . TF::WHITE . $perm->getName());
		}
		if(count($all) > 50){
			$sender->sendMessage(TF::GRAY . '  ... and ' . (count($all) - 50) . ' more. Narrow scope with /lp tree <prefix>');
		}
	}
}
