<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ApplyEditsCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.applyedits');
		$this->registerArgument(0, new RawStringArgument('code', false));
		$this->registerArgument(1, new RawStringArgument('target', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage(TextFormat::YELLOW . 'Apply-edits is not fully implemented in this PocketMine port yet.');
	}

}
