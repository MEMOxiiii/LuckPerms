<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms;

use CortexPE\Commando\BaseCommand;
use jasonw4331\LuckPerms\commands\group\GroupParentCommand;
use jasonw4331\LuckPerms\commands\misc\ApplyEditsCommand;
use jasonw4331\LuckPerms\commands\misc\EditorCommand;
use jasonw4331\LuckPerms\commands\track\TrackParentCommand;
use jasonw4331\LuckPerms\commands\user\UserParentCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LuckPermsCommand extends BaseCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.command');
		$this->registerSubCommand(new UserParentCommand('user', ''));
		$this->registerSubCommand(new GroupParentCommand('group', ''));
		$this->registerSubCommand(new TrackParentCommand('track', ''));
		$this->registerSubCommand(new EditorCommand('editor', 'Create a web editor session'));
		$this->registerSubCommand(new ApplyEditsCommand('applyedits', 'Apply changes from web editor code'));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage(TextFormat::YELLOW . 'Usage: /' . $aliasUsed . ' <user|group|track|editor|applyedits> ...');
	}

	public function getPermission() : ?string{
		return 'luckperms.command';
	}
}
