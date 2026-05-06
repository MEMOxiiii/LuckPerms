<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class ReloadConfigCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.command');
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin = LuckPerms::getInstance();
		try{
			$plugin->reloadConfig();
			$plugin->getTranslationManager()->reload();
			PermissionHelper::refreshAll($plugin);
			$sender->sendMessage(TF::GREEN . 'LuckPerms configuration reloaded.');
		}catch(\Throwable $e){
			$sender->sendMessage(TF::RED . 'Reload failed: ' . $e->getMessage());
		}
	}
}
