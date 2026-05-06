<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\tasks\SyncTask;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class SyncCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.sync');
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage(TF::YELLOW . 'Syncing data from storage...');
		try{
			(new SyncTask(LuckPerms::getInstance()))->run();
			$sender->sendMessage(TF::GREEN . 'Sync complete.');
		}catch(\Throwable $e){
			$sender->sendMessage(TF::RED . 'Sync failed: ' . $e->getMessage());
		}
	}
}
