<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function is_string;
use function time;

class TrustEditorCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.editor');
		$this->registerArgument(0, new RawStringArgument('key', false));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$key = isset($args['key']) && is_string($args['key']) ? (string) $args['key'] : '';
		if($key === ''){
			$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' trusteditor <key>');
			return;
		}
		// Store this key as a trusted editor session in the keystore
		LuckPerms::getInstance()->getWebEditorStore()->keystore()->set('trusted-' . $key, ['key' => $key, 'time' => time()]);
		$sender->sendMessage(TF::GREEN . 'Editor key ' . TF::WHITE . $key . TF::GREEN . ' has been trusted.');
		$sender->sendMessage(TF::YELLOW . 'Run ' . TF::WHITE . '/lp applyedits ' . $key . TF::YELLOW . ' to apply changes.');
	}
}
