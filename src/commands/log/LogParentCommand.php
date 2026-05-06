<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\log;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\actionlog\LoggedAction;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_reverse;
use function array_slice;
use function ceil;
use function count;
use function date;
use function is_numeric;
use function max;
use function min;
use function strtolower;

class LogParentCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.log');
		$this->registerArgument(0, new RawStringArgument('action', true));
		$this->registerArgument(1, new RawStringArgument('arg1', true));
		$this->registerArgument(2, new RawStringArgument('arg2', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$action = strtolower((string) ($args['action'] ?? ''));
		$arg1   = isset($args['arg1']) ? (string) $args['arg1'] : null;
		$arg2   = isset($args['arg2']) ? (string) $args['arg2'] : null;

		$plugin = LuckPerms::getInstance();
		$log    = $plugin->getLogDispatcher()->getLog();

		switch($action){
			case '':
			case 'recent':
				$page = ($arg1 !== null && is_numeric($arg1)) ? (int) $arg1 : 1;
				$this->showEntries($sender, array_reverse($log->getAll()), $page, 'Recent Log');
				break;

			case 'user':
				if($arg1 === null){
					$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' log user <username> [page]');
					return;
				}
				$page    = ($arg2 !== null && is_numeric($arg2)) ? (int) $arg2 : 1;
				$entries = array_reverse($log->searchByUser($arg1));
				$this->showEntries($sender, $entries, $page, 'Log for user ' . $arg1);
				break;

			case 'group':
				if($arg1 === null){
					$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' log group <groupname> [page]');
					return;
				}
				$page    = ($arg2 !== null && is_numeric($arg2)) ? (int) $arg2 : 1;
				$entries = array_reverse($log->searchByGroup($arg1));
				$this->showEntries($sender, $entries, $page, 'Log for group ' . $arg1);
				break;

			case 'notify':
				if($sender instanceof \pocketmine\player\Player){
					// Toggle notify – permissions handled by 'luckperms.log.notify'
					$sender->sendMessage(TF::YELLOW . 'Log notifications require the ' . TF::WHITE . 'luckperms.log.notify' . TF::YELLOW . ' permission.');
				}else{
					$sender->sendMessage(TF::YELLOW . 'Log notifications are sent to online players with luckperms.log.notify permission.');
				}
				break;

			default:
				$sender->sendMessage(TF::RED . "Unknown subcommand '$action'. Use: recent, user <name>, group <name>, notify");
		}
	}

	/** @param LoggedAction[] $entries */
	private function showEntries(CommandSender $sender, array $entries, int $page, string $title) : void{
		if(empty($entries)){
			$sender->sendMessage(TF::YELLOW . 'No log entries found.');
			return;
		}
		$perPage = 10;
		$total   = count($entries);
		$pages   = (int) ceil($total / $perPage);
		$page    = max(1, min($page, $pages));
		$sender->sendMessage(TF::GOLD . "--- $title [$page/$pages] (total: $total) ---");
		foreach(array_slice($entries, ($page - 1) * $perPage, $perPage) as $entry){
			$time = date('H:i:s', $entry->getTimestamp());
			$sender->sendMessage(
				TF::GRAY . "[$time] " .
				TF::DARK_GREEN . $entry->getActorName() .
				TF::GRAY . ' (' .
				TF::AQUA . $entry->getTargetType() . ': ' . $entry->getTargetName() .
				TF::GRAY . '): ' .
				TF::WHITE . $entry->getDescription()
			);
		}
	}
}
