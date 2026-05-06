<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\track;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Track;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_keys;
use function array_map;
use function count;
use function implode;
use function is_numeric;
use function strtolower;

class TrackParentCommand extends BaseSubCommand{

protected function prepare() : void{
$this->setPermission('luckperms.command');
$this->registerArgument(0, new RawStringArgument('track', true));
$this->registerArgument(1, new RawStringArgument('action', true));
$this->registerArgument(2, new RawStringArgument('arg1', true));
$this->registerArgument(3, new RawStringArgument('arg2', true));
}

public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
$plugin = LuckPerms::getInstance();
$trackName = (string) ($args['track'] ?? '');
$action = strtolower((string) ($args['action'] ?? ''));
$arg1 = isset($args['arg1']) ? (string) $args['arg1'] : null;
$arg2 = isset($args['arg2']) ? (string) $args['arg2'] : null;

if($trackName === ''){
$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' track <track> <action> ...');
return;
}

$track = $this->resolveTrack($trackName, $plugin);
if($track === null){
$sender->sendMessage(TF::RED . "Track '$trackName' not found. Use /lp createtrack <name> to create it.");
return;
}

switch($action){
case '':
				case 'info':
$this->cmdInfo($sender, $track); break;
case 'editor':
$sender->sendMessage(TF::YELLOW . 'Use ' . TF::WHITE . '/lp editor' . TF::YELLOW . ' for a full web editor session.'); break;
case 'append':
if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' track ' . $trackName . ' append <group>'); return; }
$plugin->getGroupManager()->getOrMake($arg1);
if($track->containsGroup($arg1)){ $sender->sendMessage(TF::YELLOW . "Group '$arg1' is already in track $trackName."); return; }
$track->appendGroup($arg1);
$this->saveTrack($track, $plugin);
$sender->sendMessage(TF::GREEN . 'Appended group ' . TF::WHITE . $arg1 . TF::GREEN . ' to track ' . $trackName . '.'); break;
case 'insert':
if($arg1 === null || $arg2 === null || !is_numeric($arg2)){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' track ' . $trackName . ' insert <group> <position>'); return; }
$plugin->getGroupManager()->getOrMake($arg1);
if($track->containsGroup($arg1)){ $sender->sendMessage(TF::YELLOW . "Group '$arg1' is already in track $trackName."); return; }
$track->insertGroup($arg1, (int) $arg2);
$this->saveTrack($track, $plugin);
$sender->sendMessage(TF::GREEN . 'Inserted group ' . TF::WHITE . $arg1 . TF::GREEN . ' at position ' . $arg2 . ' in track ' . $trackName . '.'); break;
case 'remove':
if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' track ' . $trackName . ' remove <group>'); return; }
if(!$track->containsGroup($arg1)){ $sender->sendMessage(TF::YELLOW . "Group '$arg1' is not in track $trackName."); return; }
$track->removeGroup($arg1);
$this->saveTrack($track, $plugin);
$sender->sendMessage(TF::GREEN . 'Removed group ' . TF::WHITE . $arg1 . TF::GREEN . ' from track ' . $trackName . '.'); break;
case 'clear':
$track->setGroups([]);
$this->saveTrack($track, $plugin);
$sender->sendMessage(TF::GREEN . 'Cleared all groups from track ' . $trackName . '.'); break;
case 'rename':
if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' track ' . $trackName . ' rename <new-name>'); return; }
$existing = $this->resolveTrack($arg1, $plugin);
if($existing !== null){ $sender->sendMessage(TF::RED . "Track '$arg1' already exists."); return; }
$newTrack = new Track($arg1);
$newTrack->setGroups($track->getGroups());
$plugin->getStorage()->saveTrack($newTrack);
$plugin->getStorage()->deleteTrack($track->getName());
$sender->sendMessage(TF::GREEN . 'Renamed track ' . TF::WHITE . $trackName . TF::GREEN . ' to ' . TF::WHITE . $arg1 . TF::GREEN . '.'); break;
case 'clone':
if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' track ' . $trackName . ' clone <new-name>'); return; }
$clone = new Track($arg1);
$clone->setGroups($track->getGroups());
$plugin->getStorage()->saveTrack($clone);
$sender->sendMessage(TF::GREEN . 'Cloned track ' . TF::WHITE . $trackName . TF::GREEN . ' to ' . TF::WHITE . $arg1 . TF::GREEN . '.'); break;
default:
$sender->sendMessage(TF::RED . "Unknown action '$action'. Use: info, editor, append, insert, remove, clear, rename, clone");
}
}

private function resolveTrack(string $name, LuckPerms $plugin) : ?Track{
foreach($plugin->getTrackManager()->getAll() as $track){
if(strtolower($track->getName()) === strtolower($name)) return $track;
}
return null;
}

private function saveTrack(Track $track, LuckPerms $plugin) : void{
$plugin->getStorage()->saveTrack($track);
}

private function cmdInfo(CommandSender $sender, Track $track) : void{
$groups = $track->getGroups();
$sender->sendMessage(TF::GOLD . '=== Track Info: ' . TF::WHITE . $track->getName() . TF::GOLD . ' ===');
if(count($groups) === 0){
$sender->sendMessage(TF::YELLOW . 'Groups: ' . TF::GRAY . '(none)');
}else{
$numbered = array_map(static fn(int $i, string $g) => TF::WHITE . ($i + 1) . '. ' . TF::GREEN . $g, array_keys($groups), $groups);
$sender->sendMessage(TF::YELLOW . 'Groups (' . count($groups) . '): ' . TF::WHITE . implode(TF::GRAY . ' -> ', array_map(static fn(string $g) => TF::GREEN . $g, $groups)));
foreach($numbered as $line) $sender->sendMessage($line);
}
}
}
