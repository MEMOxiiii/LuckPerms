<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\actionlog;

use jasonw4331\LuckPerms\LuckPerms;

/**
 * Dispatches log entries to the in-memory log and any registered log notifiers.
 */
class LogDispatcher{
	private Log $log;

	public function __construct(private LuckPerms $plugin){
		$this->log = new Log();
	}

	public function getLog() : Log{
		return $this->log;
	}

	public function dispatch(LoggedAction $action) : void{
		$this->log->add($action);
		// Notify players with luckperms.log.notify permission
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($player->hasPermission('luckperms.log.notify')){
				$player->sendMessage(
					\pocketmine\utils\TextFormat::GRAY . '[LP] ' .
					\pocketmine\utils\TextFormat::DARK_GREEN . $action->getActorName() .
					\pocketmine\utils\TextFormat::GRAY . ' -> ' .
					\pocketmine\utils\TextFormat::AQUA . '[' . $action->getTargetType() . '] ' . $action->getTargetName() .
					\pocketmine\utils\TextFormat::GRAY . ': ' .
					\pocketmine\utils\TextFormat::WHITE . $action->getDescription()
				);
			}
		}
	}

	public function close() : void{
		// nothing to flush; in-memory only
	}
}
