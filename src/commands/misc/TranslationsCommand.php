<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function count;
use function implode;

class TranslationsCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.command');
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin = LuckPerms::getInstance();
		$installed = $plugin->getTranslationManager()->getInstalledLocales();
		$sender->sendMessage(TF::GOLD . '--- LuckPerms Translations ---');
		$sender->sendMessage(TF::YELLOW . 'Installed locales: ' . TF::WHITE . (count($installed) > 0 ? implode(', ', $installed) : 'en (default)'));
		$sender->sendMessage(TF::GRAY . 'Translation files are stored in: ' . $plugin->getTranslationManager()->getTranslationsDirectory());
		$sender->sendMessage(TF::GRAY . 'Use /lp reload to apply after changing language settings.');
	}
}
