<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use jasonw4331\LuckPerms\commands\group\GroupParentCommand;
use jasonw4331\LuckPerms\commands\log\LogParentCommand;
use jasonw4331\LuckPerms\commands\misc\ApplyEditsCommand;
use jasonw4331\LuckPerms\commands\misc\EditorCommand;
use jasonw4331\LuckPerms\commands\misc\ExportCommand;
use jasonw4331\LuckPerms\commands\misc\ImportCommand;
use jasonw4331\LuckPerms\commands\misc\InfoCommand;
use jasonw4331\LuckPerms\commands\misc\SearchCommand;
use jasonw4331\LuckPerms\commands\misc\VerboseCommand;
use jasonw4331\LuckPerms\commands\track\TrackParentCommand;
use jasonw4331\LuckPerms\commands\user\UserParentCommand;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\model\Track;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_map;
use function count;
use function implode;
use function strtolower;

class LuckPermsCommand extends BaseCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.command');
		$this->registerSubCommand(new UserParentCommand('user', 'Manage a specific user'));
		$this->registerSubCommand(new GroupParentCommand('group', 'Manage a specific group'));
		$this->registerSubCommand(new TrackParentCommand('track', 'Manage a specific track'));
		$this->registerSubCommand(new EditorCommand('editor', 'Create a web editor session'));
		$this->registerSubCommand(new ApplyEditsCommand('applyedits', 'Apply changes from web editor code'));
		$this->registerSubCommand(new SearchCommand('search', 'Search for nodes'));
		$this->registerSubCommand(new ExportCommand('export', 'Export data to a file'));
		$this->registerSubCommand(new ImportCommand('import', 'Import data from a file'));
		$this->registerSubCommand(new LogParentCommand('log', 'View the action log'));
		$this->registerSubCommand(new InfoCommand('info', 'Show plugin info'));
		$this->registerSubCommand(new VerboseCommand('verbose', 'Show effective permissions for a player/group'));
		// Extra arguments for top-level sub-commands handled in onRun
		$this->registerArgument(0, new RawStringArgument('cmd', true));
		$this->registerArgument(1, new RawStringArgument('arg1', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin = LuckPerms::getInstance();
		$cmd  = strtolower((string) ($args['cmd'] ?? ''));
		$arg1 = isset($args['arg1']) ? (string) $args['arg1'] : null;

		switch($cmd){
			case 'creategroup':
				if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' creategroup <name>'); return; }
				$name = strtolower($arg1);
				if($plugin->getGroupManager()->getIfLoaded($name) !== null){ $sender->sendMessage(TF::RED . "Group '$name' already exists."); return; }
				$group = $plugin->getGroupManager()->getOrMake($name);
				$plugin->getStorage()->saveGroup($group);
				$sender->sendMessage(TF::GREEN . "Created group '$name'.");
				break;
			case 'deletegroup':
				if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' deletegroup <name>'); return; }
				$name = strtolower($arg1);
				if($plugin->getGroupManager()->getIfLoaded($name) === null){ $sender->sendMessage(TF::RED . "Group '$name' not found."); return; }
				$plugin->getStorage()->deleteGroup($name);
				$sender->sendMessage(TF::GREEN . "Deleted group '$name'.");
				break;
			case 'listgroups':
				$groups = $plugin->getGroupManager()->getAll();
				if(empty($groups)){ $sender->sendMessage(TF::YELLOW . 'No groups defined.'); return; }
				$sender->sendMessage(TF::GOLD . 'Groups (' . count($groups) . '): ' . TF::WHITE . implode(', ', array_map(static fn(Group $g) => $g->getName(), $groups)));
				break;
			case 'createtrack':
				if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' createtrack <name>'); return; }
				$name = strtolower($arg1);
				foreach($plugin->getTrackManager()->getAll() as $t){ if(strtolower($t->getName()) === $name){ $sender->sendMessage(TF::RED . "Track '$name' already exists."); return; } }
				$track = new Track($name);
				$plugin->getStorage()->saveTrack($track);
				$sender->sendMessage(TF::GREEN . "Created track '$name'.");
				break;
			case 'deletetrack':
				if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' deletetrack <name>'); return; }
				$name = strtolower($arg1);
				$found = false;
				foreach($plugin->getTrackManager()->getAll() as $t){ if(strtolower($t->getName()) === $name){ $found = true; break; } }
				if(!$found){ $sender->sendMessage(TF::RED . "Track '$name' not found."); return; }
				$plugin->getStorage()->deleteTrack($name);
				$sender->sendMessage(TF::GREEN . "Deleted track '$name'.");
				break;
			case 'listtracks':
				$tracks = $plugin->getTrackManager()->getAll();
				if(empty($tracks)){ $sender->sendMessage(TF::YELLOW . 'No tracks defined.'); return; }
				$sender->sendMessage(TF::GOLD . 'Tracks (' . count($tracks) . '): ' . TF::WHITE . implode(', ', array_map(static fn(Track $t) => $t->getName(), $tracks)));
				break;
			case 'sync':
				try{ (new \jasonw4331\LuckPerms\tasks\SyncTask($plugin))->run(); $sender->sendMessage(TF::GREEN . 'Sync complete.'); }
				catch(\Throwable $e){ $sender->sendMessage(TF::RED . 'Sync failed: ' . $e->getMessage()); }
				break;
			case 'info':
				$sender->sendMessage(TF::GOLD . '=== LuckPerms Info ===' );
				$sender->sendMessage(TF::YELLOW . 'Version: ' . TF::WHITE . $plugin->getDescription()->getVersion());
				$sender->sendMessage(TF::YELLOW . 'Groups loaded: ' . TF::WHITE . count($plugin->getGroupManager()->getAll()));
				$sender->sendMessage(TF::YELLOW . 'Tracks loaded: ' . TF::WHITE . count($plugin->getTrackManager()->getAll()));
				$sender->sendMessage(TF::YELLOW . 'Users cached: ' . TF::WHITE . count($plugin->getUserManager()->getAll()));
				break;
			default:
				$sender->sendMessage(TF::YELLOW . 'LuckPerms commands:');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' user <name> <action> ...');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' group <name> <action> ...');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' track <name> <action> ...');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' editor');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' applyedits <code>');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' search <node|wildcard> [page]');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' log [recent|user <name>|group <name>]');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' verbose <player|group:<name>>');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' export [filename]');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' import <filename>');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' creategroup|deletegroup|listgroups');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' createtrack|deletetrack|listtracks');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' sync | info');
		}
	}

	public function getPermission() : ?string{
		return 'luckperms.command';
	}
}
