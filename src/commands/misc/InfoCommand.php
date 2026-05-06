<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function count;
use function memory_get_usage;
use function round;

class InfoCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.info');
	}

	/** @param array<mixed> $args */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin = LuckPerms::getInstance();

		$sender->sendMessage(TF::GOLD . '=====================================');
		$sender->sendMessage(TF::DARK_GREEN . '  LuckPerms ' . TF::YELLOW . 'v' . $plugin->getDescription()->getVersion());
		$sender->sendMessage(TF::GOLD . '=====================================');
		$sender->sendMessage(TF::YELLOW . 'Storage: ' . TF::WHITE . 'JSON (file-based)');
		$sender->sendMessage(TF::YELLOW . 'Groups loaded: ' . TF::WHITE . count($plugin->getGroupManager()->getAll()));
		$sender->sendMessage(TF::YELLOW . 'Tracks loaded: ' . TF::WHITE . count($plugin->getTrackManager()->getAll()));
		$sender->sendMessage(TF::YELLOW . 'Users cached: ' . TF::WHITE . count($plugin->getUserManager()->getAll()));
		$sender->sendMessage(TF::YELLOW . 'Online players: ' . TF::WHITE . count($plugin->getServer()->getOnlinePlayers()));
		$sender->sendMessage(TF::YELLOW . 'Log entries: ' . TF::WHITE . count($plugin->getLogDispatcher()->getLog()->getAll()));
		$memMb = round(memory_get_usage(true) / 1024 / 1024, 1);
		$sender->sendMessage(TF::YELLOW . 'Memory: ' . TF::WHITE . $memMb . ' MB');
		$sender->sendMessage(TF::GOLD . '=====================================');
	}
}
