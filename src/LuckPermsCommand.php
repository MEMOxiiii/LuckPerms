<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use jasonw4331\LuckPerms\commands\group\GroupParentCommand;
use jasonw4331\LuckPerms\commands\log\LogParentCommand;
use jasonw4331\LuckPerms\commands\misc\ApplyEditsCommand;
use jasonw4331\LuckPerms\commands\misc\BulkUpdateCommand;
use jasonw4331\LuckPerms\commands\misc\EditorCommand;
use jasonw4331\LuckPerms\commands\misc\ExportCommand;
use jasonw4331\LuckPerms\commands\misc\ImportCommand;
use jasonw4331\LuckPerms\commands\misc\InfoCommand;
use jasonw4331\LuckPerms\commands\misc\NetworkSyncCommand;
use jasonw4331\LuckPerms\commands\misc\ReloadConfigCommand;
use jasonw4331\LuckPerms\commands\misc\SearchCommand;
use jasonw4331\LuckPerms\commands\misc\SyncCommand;
use jasonw4331\LuckPerms\commands\misc\TranslationsCommand;
use jasonw4331\LuckPerms\commands\misc\TreeCommand;
use jasonw4331\LuckPerms\commands\misc\TrustEditorCommand;
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
		$this->registerSubCommand(new TrustEditorCommand('trusteditor', 'Trust a web editor session key'));
		$this->registerSubCommand(new ApplyEditsCommand('applyedits', 'Apply changes from web editor code'));
		$this->registerSubCommand(new SearchCommand('search', 'Search for nodes'));
		$this->registerSubCommand(new ExportCommand('export', 'Export data to a file'));
		$this->registerSubCommand(new ImportCommand('import', 'Import data from a file'));
		$this->registerSubCommand(new LogParentCommand('log', 'View the action log'));
		$this->registerSubCommand(new InfoCommand('info', 'Show plugin info'));
		$this->registerSubCommand(new VerboseCommand('verbose', 'Show effective permissions for a player/group'));
		$this->registerSubCommand(new SyncCommand('sync', 'Reload all data from storage'));
		$this->registerSubCommand(new NetworkSyncCommand('networksync', 'Sync data across the network'));
		$this->registerSubCommand(new ReloadConfigCommand('reload', 'Reload the plugin configuration'));
		$this->registerSubCommand(new TreeCommand('tree', 'View the permission tree'));
		$this->registerSubCommand(new BulkUpdateCommand('bulkupdate', 'Perform a bulk update on permission nodes'));
		$this->registerSubCommand(new TranslationsCommand('translations', 'View installed translations'));
		// Extra arguments for top-level sub-commands handled in onRun
		$this->registerArgument(0, new RawStringArgument('cmd', true));
		$this->registerArgument(1, new RawStringArgument('arg1', true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$plugin = LuckPerms::getInstance();
		$cmd = strtolower((string) ($args['cmd'] ?? ''));
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
				if(count($groups) === 0){ $sender->sendMessage(TF::YELLOW . 'No groups defined.'); return; }
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
				if(count($tracks) === 0){ $sender->sendMessage(TF::YELLOW . 'No tracks defined.'); return; }
				$sender->sendMessage(TF::GOLD . 'Tracks (' . count($tracks) . '): ' . TF::WHITE . implode(', ', array_map(static fn(Track $t) => $t->getName(), $tracks)));
				break;
			case 'sync':
				// handled by SyncCommand subcommand — fall through to help
			case 'info':
				// handled by InfoCommand subcommand — fall through to help
			default:
				$sender->sendMessage(TF::YELLOW . 'LuckPerms commands:');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' user <name> <action> ...');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' group <name> <action> ...');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' track <name> <action> ...');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' creategroup|deletegroup|listgroups');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' createtrack|deletetrack|listtracks');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' editor | trusteditor <key> | applyedits <code>');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' search <node>');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' log [recent|user <n>|group <n>]');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' verbose <player|group:<n>>');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' export [file] | import <file>');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' sync | networksync | reload | info');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' tree [scope] | translations');
				$sender->sendMessage(TF::GOLD . '  /' . $aliasUsed . ' bulkupdate <all|user|group> <delete|replace> <node> [new]');
		}
	}

	public function getPermission() : ?string{
		return 'luckperms.command';
	}
}
