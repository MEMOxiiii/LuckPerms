<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\user;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\User;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use Ramsey\Uuid\Uuid;
use function array_filter;
use function array_map;
use function array_slice;
use function array_values;
use function ceil;
use function count;
use function date;
use function implode;
use function in_array;
use function is_numeric;
use function max;
use function min;
use function str_starts_with;
use function strlen;
use function strtolower;
use function substr;
use function time;

class UserParentCommand extends BaseSubCommand{

protected function prepare() : void{
$this->setPermission('luckperms.command');
$this->registerArgument(0, new RawStringArgument('user', true));
$this->registerArgument(1, new RawStringArgument('action', true));
$this->registerArgument(2, new RawStringArgument('sub', true));
$this->registerArgument(3, new RawStringArgument('arg1', true));
$this->registerArgument(4, new RawStringArgument('arg2', true));
$this->registerArgument(5, new RawStringArgument('arg3', true));
$this->registerArgument(6, new RawStringArgument('arg4', true));
}

public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
$plugin = LuckPerms::getInstance();
$username = (string) ($args['user'] ?? '');
$action = strtolower((string) ($args['action'] ?? ''));
$sub = strtolower((string) ($args['sub'] ?? ''));
$arg1 = isset($args['arg1']) ? (string) $args['arg1'] : null;
$arg2 = isset($args['arg2']) ? (string) $args['arg2'] : null;
$arg3 = isset($args['arg3']) ? (string) $args['arg3'] : null;

if($username === ''){
$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' user <user> <action> ...');
return;
}

$user = $this->resolveUser($username, $plugin);
if($user === null){
$sender->sendMessage(TF::RED . "User '$username' not found. They must have joined at least once.");
return;
}

match($action){
'', 'info' => $this->cmdInfo($sender, $user, $plugin),
'editor' => $this->cmdEditor($sender, $user),
'showtracks' => $this->cmdShowTracks($sender, $user, $plugin),
'clear' => $this->cmdClear($sender, $user, $plugin),
'clone' => $sub !== '' ? $this->cmdClone($sender, $user, $sub, $plugin) : $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' user ' . $username . ' clone <target>'),
'promote', 'demote' => $sub !== '' ? $this->cmdPromoteDemote($sender, $user, $action, $sub, $plugin) : $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' user ' . $username . ' ' . $action . ' <track>'),
'permission' => $this->handlePermission($sender, $aliasUsed, $username, $user, $sub, $arg1, $arg2, $arg3, $plugin),
'parent' => $this->handleParent($sender, $aliasUsed, $username, $user, $sub, $arg1, $arg2, $plugin),
'meta' => $this->handleMeta($sender, $aliasUsed, $username, $user, $sub, $arg1, $arg2, $arg3, $plugin),
default => $sender->sendMessage(TF::RED . "Unknown action '$action'. Use: info, editor, showtracks, clear, clone, promote, demote, permission, parent, meta"),
};
}

private function resolveUser(string $nameOrUuid, LuckPerms $plugin) : ?User{
$player = $plugin->getServer()->getPlayerExact($nameOrUuid);
if($player !== null){
$user = $plugin->getUserManager()->getIfLoaded($player->getUniqueId());
if($user !== null) return $user;
$loaded = $plugin->getStorage()->loadUser($player->getUniqueId());
if($loaded !== null){
$u = $plugin->getUserManager()->load($player->getUniqueId(), $player->getName());
$u->setNodes($loaded->getNodes());
return $u;
}
return $plugin->getUserManager()->load($player->getUniqueId(), $player->getName());
}
try{
$uuid = Uuid::fromString($nameOrUuid);
$user = $plugin->getUserManager()->getIfLoaded($uuid);
if($user !== null) return $user;
return $plugin->getStorage()->loadUser($uuid);
}catch(\Throwable){}
$uuid = $plugin->getStorage()->getPlayerUniqueId($nameOrUuid);
if($uuid !== null){
$user = $plugin->getUserManager()->getIfLoaded($uuid);
if($user !== null) return $user;
return $plugin->getStorage()->loadUser($uuid);
}
return null;
}

private function saveAndRefresh(User $user, LuckPerms $plugin, CommandSender $sender) : void{
$plugin->getStorage()->saveUser($user);
$player = $plugin->getServer()->getPlayerExact($user->getUsername());
if($player !== null) PermissionHelper::applyPermissions($player, $user, $plugin);
}

private function removeNodeByKey(User $user, string $key) : bool{
$key = strtolower($key);
$nodes = $user->getNodes();
$new = array_values(array_filter($nodes, static fn(NodeEntry $n) => strtolower($n->getKey()) !== $key));
$user->setNodes($new);
return count($new) < count($nodes);
}

/* info */
private function cmdInfo(CommandSender $sender, User $user, LuckPerms $plugin) : void{
$nodes = $user->getNodes();
$parents = array_filter($nodes, static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.'));
$perms = array_filter($nodes, static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.'));
$sender->sendMessage(TF::GOLD . '=== User Info: ' . TF::WHITE . $user->getUsername() . TF::GOLD . ' ===');
$sender->sendMessage(TF::YELLOW . 'UUID: ' . TF::WHITE . $user->getUniqueId()->toString());
if(count($parents) > 0){
$list = implode(', ', array_map(static fn(NodeEntry $n) => substr($n->getKey(), 6), $parents));
$sender->sendMessage(TF::YELLOW . 'Groups: ' . TF::WHITE . $list);
}else{
$sender->sendMessage(TF::YELLOW . 'Groups: ' . TF::GRAY . '(none)');
}
$sender->sendMessage(TF::YELLOW . 'Permissions: ' . TF::WHITE . count($perms) . ' node(s)');
}

/* editor */
private function cmdEditor(CommandSender $sender, User $user) : void{
$sender->sendMessage(TF::YELLOW . 'Per-user editor: use ' . TF::WHITE . '/lp editor' . TF::YELLOW . ' for a full web editor session.');
}

/* showtracks */
private function cmdShowTracks(CommandSender $sender, User $user, LuckPerms $plugin) : void{
$tracks = $plugin->getTrackManager()->getAll();
if(count($tracks) === 0){ $sender->sendMessage(TF::YELLOW . 'No tracks defined.'); return; }
$userGroups = array_map(static fn(NodeEntry $n) => strtolower(substr($n->getKey(), 6)),
array_filter($user->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.')));
foreach($tracks as $track){
if(count($track->getGroups()) === 0) continue;
$rendered = array_map(static fn(string $g) => (in_array(strtolower($g), array_values($userGroups), true) ? TF::GREEN : TF::GRAY) . $g . TF::RESET, $track->getGroups());
$sender->sendMessage(TF::YELLOW . $track->getName() . ': ' . implode(TF::WHITE . ' > ', $rendered));
}
}

/* clear */
private function cmdClear(CommandSender $sender, User $user, LuckPerms $plugin) : void{
$count = count($user->getNodes());
$user->setNodes([]);
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . "Cleared $count node(s) from " . $user->getUsername() . '.');
}

/* clone */
private function cmdClone(CommandSender $sender, User $user, string $targetName, LuckPerms $plugin) : void{
$target = $this->resolveUser($targetName, $plugin);
if($target === null){ $sender->sendMessage(TF::RED . "Target user '$targetName' not found."); return; }
$target->setNodes($user->getNodes());
$this->saveAndRefresh($target, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Cloned permissions from ' . $user->getUsername() . ' to ' . $target->getUsername() . '.');
}

/* promote / demote */
private function cmdPromoteDemote(CommandSender $sender, User $user, string $dir, string $trackName, LuckPerms $plugin) : void{
$track = null;
foreach($plugin->getTrackManager()->getAll() as $t){
if(strtolower($t->getName()) === strtolower($trackName)){ $track = $t; break; }
}
if($track === null){ $sender->sendMessage(TF::RED . "Track '$trackName' not found."); return; }
$groups = $track->getGroups();
if(count($groups) === 0){ $sender->sendMessage(TF::RED . "Track '$trackName' has no groups."); return; }
$currentIdx = -1;
foreach($user->getNodes() as $node){
if(!str_starts_with(strtolower($node->getKey()), 'group.')) continue;
$g = strtolower(substr($node->getKey(), 6));
$idx = $track->indexOf($g);
if($idx !== -1) $currentIdx = $idx;
}
if($dir === 'promote'){
$next = $currentIdx + 1;
if($next >= count($groups)){ $sender->sendMessage(TF::YELLOW . $user->getUsername() . ' is already at the top.'); return; }
if($currentIdx !== -1) $this->removeNodeByKey($user, 'group.' . strtolower($groups[$currentIdx]));
$user->addNode(new NodeEntry('group.' . strtolower($groups[$next]), true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Promoted ' . $user->getUsername() . ' to ' . TF::WHITE . $groups[$next] . TF::GREEN . ' on track ' . $track->getName() . '.');
}else{
if($currentIdx <= 0){ $sender->sendMessage(TF::YELLOW . $user->getUsername() . ' is already at the bottom.'); return; }
$this->removeNodeByKey($user, 'group.' . strtolower($groups[$currentIdx]));
$user->addNode(new NodeEntry('group.' . strtolower($groups[$currentIdx - 1]), true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Demoted ' . $user->getUsername() . ' to ' . TF::WHITE . $groups[$currentIdx - 1] . TF::GREEN . ' on track ' . $track->getName() . '.');
}
}

/* ─── permission ─── */
private function handlePermission(CommandSender $sender, string $al, string $un, User $user, string $sub, ?string $a1, ?string $a2, ?string $a3, LuckPerms $plugin) : void{
switch($sub){
case '':
				case 'info':
$page = is_numeric($a1) ? (int) $a1 : 1;
$nodes = array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.')));
if(count($nodes) === 0){ $sender->sendMessage(TF::YELLOW . $user->getUsername() . ' has no permission nodes.'); return; }
$perPage = 12; $pages = (int) ceil(count($nodes) / $perPage); $page = max(1, min($page, $pages));
$sender->sendMessage(TF::GOLD . '--- Permissions (' . $user->getUsername() . ') [' . $page . '/' . $pages . '] ---');
foreach(array_slice($nodes, ($page - 1) * $perPage, $perPage) as $n){
$sender->sendMessage(($n->getValue() ? TF::GREEN : TF::RED) . '  ' . $n->getKey() . ($n->isTemporary() ? TF::GRAY . ' (' . date('Y-m-d H:i', $n->getExpiry() ?? 0) . ')' : ''));
}
break;
case 'set':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' permission set <node> [true|false]'); return; }
$val = ($a2 === null || strtolower($a2) !== 'false');
$this->removeNodeByKey($user, $a1);
$user->addNode(new NodeEntry($a1, $val, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set ' . TF::WHITE . $a1 . TF::GREEN . ' = ' . ($val ? 'true' : 'false') . ' for ' . $user->getUsername() . '.');
break;
case 'unset':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' permission unset <node>'); return; }
if($this->removeNodeByKey($user, $a1)){ $this->saveAndRefresh($user, $plugin, $sender); $sender->sendMessage(TF::GREEN . 'Unset ' . TF::WHITE . $a1 . TF::GREEN . ' from ' . $user->getUsername() . '.'); }
else $sender->sendMessage(TF::YELLOW . 'Node ' . TF::WHITE . $a1 . TF::YELLOW . ' not found on ' . $user->getUsername() . '.');
break;
case 'settemp':
if($a1 === null || $a2 === null || $a3 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' permission settemp <node> <true|false> <seconds>'); return; }
$val = strtolower($a2) !== 'false'; $dur = is_numeric($a3) ? (int) $a3 : 0;
if($dur <= 0){ $sender->sendMessage(TF::RED . 'Duration must be positive.'); return; }
$this->removeNodeByKey($user, $a1);
$user->addNode(new NodeEntry($a1, $val, [], time() + $dur));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set temp ' . TF::WHITE . $a1 . TF::GREEN . ' (' . $dur . 's) for ' . $user->getUsername() . '.');
break;
case 'unsettemp':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' permission unsettemp <node>'); return; }
$nodes = $user->getNodes(); $before = count($nodes);
$user->setNodes(array_values(array_filter($nodes, static fn(NodeEntry $n) => strtolower($n->getKey()) !== strtolower($a1) || !$n->isTemporary())));
if(count($user->getNodes()) < $before){ $this->saveAndRefresh($user, $plugin, $sender); $sender->sendMessage(TF::GREEN . 'Removed temp node ' . TF::WHITE . $a1 . TF::GREEN . ' from ' . $user->getUsername() . '.'); }
else $sender->sendMessage(TF::YELLOW . 'No matching temporary node found.');
break;
case 'check':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' permission check <node>'); return; }
$eff = PermissionHelper::resolveEffectivePermissions($user, $plugin);
$has = $eff[strtolower($a1)] ?? false;
$sender->sendMessage(TF::YELLOW . $user->getUsername() . ' -> ' . $a1 . ': ' . ($has ? TF::GREEN . 'true' : TF::RED . 'false'));
break;
case 'clear':
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.'))));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Cleared permission nodes from ' . $user->getUsername() . '.');
break;
default:
$sender->sendMessage(TF::RED . "Unknown: 'permission $sub'. Use: info, set, unset, settemp, unsettemp, check, clear");
}
}

/* ─── parent ─── */
private function handleParent(CommandSender $sender, string $al, string $un, User $user, string $sub, ?string $a1, ?string $a2, LuckPerms $plugin) : void{
switch($sub){
case '':
				case 'info':
$page = is_numeric($a1) ? (int) $a1 : 1;
$nodes = array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.')));
if(count($nodes) === 0){ $sender->sendMessage(TF::YELLOW . $user->getUsername() . ' has no parent groups.'); return; }
$perPage = 12; $pages = (int) ceil(count($nodes) / $perPage); $page = max(1, min($page, $pages));
$sender->sendMessage(TF::GOLD . '--- Parents (' . $user->getUsername() . ') [' . $page . '/' . $pages . '] ---');
foreach(array_slice($nodes, ($page - 1) * $perPage, $perPage) as $n){
$g = substr($n->getKey(), 6);
$sender->sendMessage(TF::GREEN . '  - ' . $g . ($n->isTemporary() ? TF::GRAY . ' (' . date('Y-m-d H:i', $n->getExpiry() ?? 0) . ')' : ''));
}
break;
case 'add':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent add <group>'); return; }
$plugin->getGroupManager()->getOrMake($a1);
$nk = 'group.' . strtolower($a1);
$this->removeNodeByKey($user, $nk);
$user->addNode(new NodeEntry($nk, true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Added ' . $user->getUsername() . ' to group ' . TF::WHITE . $a1 . TF::GREEN . '.');
break;
case 'remove':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent remove <group>'); return; }
if($this->removeNodeByKey($user, 'group.' . strtolower($a1))){ $this->saveAndRefresh($user, $plugin, $sender); $sender->sendMessage(TF::GREEN . 'Removed ' . $user->getUsername() . ' from group ' . TF::WHITE . $a1 . TF::GREEN . '.'); }
else $sender->sendMessage(TF::YELLOW . $user->getUsername() . ' is not in group ' . TF::WHITE . $a1 . TF::YELLOW . '.');
break;
case 'set':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent set <group>'); return; }
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.'))));
$plugin->getGroupManager()->getOrMake($a1);
$user->addNode(new NodeEntry('group.' . strtolower($a1), true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set ' . $user->getUsername() . "'s primary group to " . TF::WHITE . $a1 . TF::GREEN . '.');
break;
case 'clear':
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.'))));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Cleared all parent groups from ' . $user->getUsername() . '.');
break;
case 'addtemp':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent addtemp <group> <seconds>'); return; }
$dur = is_numeric($a2) ? (int) $a2 : 0;
if($dur <= 0){ $sender->sendMessage(TF::RED . 'Duration must be positive.'); return; }
$plugin->getGroupManager()->getOrMake($a1);
$nk = 'group.' . strtolower($a1);
$this->removeNodeByKey($user, $nk);
$user->addNode(new NodeEntry($nk, true, [], time() + $dur));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Added ' . $user->getUsername() . ' to group ' . TF::WHITE . $a1 . TF::GREEN . ' (' . $dur . 's).');
break;
case 'removetemp':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent removetemp <group>'); return; }
$nk = 'group.' . strtolower($a1); $nodes = $user->getNodes(); $before = count($nodes);
$user->setNodes(array_values(array_filter($nodes, static fn(NodeEntry $n) => strtolower($n->getKey()) !== $nk || !$n->isTemporary())));
if(count($user->getNodes()) < $before){ $this->saveAndRefresh($user, $plugin, $sender); $sender->sendMessage(TF::GREEN . 'Removed temp group ' . TF::WHITE . $a1 . TF::GREEN . ' from ' . $user->getUsername() . '.'); }
else $sender->sendMessage(TF::YELLOW . 'No matching temporary group found.');
break;
case 'settrack':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent settrack <track> <group>'); return; }
$trackObj = null;
foreach($plugin->getTrackManager()->getAll() as $t){ if(strtolower($t->getName()) === strtolower($a1)){ $trackObj = $t; break; } }
if($trackObj === null){ $sender->sendMessage(TF::RED . "Track '$a1' not found."); return; }
if(!in_array(strtolower($a2), array_map('strtolower', $trackObj->getGroups()), true)){ $sender->sendMessage(TF::RED . "Group '$a2' is not in track '$a1'."); return; }
$trackGroups = array_map('strtolower', $trackObj->getGroups());
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.') || !in_array(strtolower(substr($n->getKey(), 6)), $trackGroups, true))));
$plugin->getGroupManager()->getOrMake($a2);
$user->addNode(new NodeEntry('group.' . strtolower($a2), true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set ' . $user->getUsername() . ' to group ' . TF::WHITE . $a2 . TF::GREEN . " on track $a1.");
break;
case 'cleartrack':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent cleartrack <track>'); return; }
$trackObj2 = null;
foreach($plugin->getTrackManager()->getAll() as $t){ if(strtolower($t->getName()) === strtolower($a1)){ $trackObj2 = $t; break; } }
if($trackObj2 === null){ $sender->sendMessage(TF::RED . "Track '$a1' not found."); return; }
$trackGroups2 = array_map('strtolower', $trackObj2->getGroups());
$beforeCt = count($user->getNodes());
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.') || !in_array(strtolower(substr($n->getKey(), 6)), $trackGroups2, true))));
$removed = $beforeCt - count($user->getNodes());
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . "Removed $removed group(s) on track $a1 from " . $user->getUsername() . '.');
break;
case 'switchprimarygroup':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' parent switchprimarygroup <group>'); return; }
$parentNodes = array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.')));
if(count($parentNodes) === 1) $this->removeNodeByKey($user, $parentNodes[0]->getKey());
$plugin->getGroupManager()->getOrMake($a1);
$nkSp = 'group.' . strtolower($a1);
$this->removeNodeByKey($user, $nkSp);
$user->addNode(new NodeEntry($nkSp, true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Switched ' . $user->getUsername() . "'s primary group to " . TF::WHITE . $a1 . TF::GREEN . '.');
break;
default:
$sender->sendMessage(TF::RED . "Unknown: 'parent $sub'. Use: info, add, remove, set, clear, addtemp, removetemp, settrack, cleartrack, switchprimarygroup");
}
}

/* ─── meta ─── */
private function handleMeta(CommandSender $sender, string $al, string $un, User $user, string $sub, ?string $a1, ?string $a2, ?string $a3, LuckPerms $plugin) : void{
switch($sub){
case '':
				case 'info':
$nodes = array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'meta.') || str_starts_with(strtolower($n->getKey()), 'prefix.') || str_starts_with(strtolower($n->getKey()), 'suffix.')));
if(count($nodes) === 0){ $sender->sendMessage(TF::YELLOW . $user->getUsername() . ' has no meta.'); return; }
$sender->sendMessage(TF::GOLD . '--- Meta (' . $user->getUsername() . ') ---');
foreach($nodes as $n) $sender->sendMessage(TF::YELLOW . '  - ' . TF::WHITE . $n->getKey());
break;
case 'set':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta set <key> <value>'); return; }
$nodes = array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.' . strtolower($a1) . '.')));
$user->setNodes($nodes);
$user->addNode(new NodeEntry('meta.' . $a1 . '.' . $a2, true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set meta ' . TF::WHITE . $a1 . TF::GREEN . ' = ' . TF::WHITE . $a2 . TF::GREEN . ' for ' . $user->getUsername() . '.');
break;
case 'unset':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta unset <key>'); return; }
$nodes = $user->getNodes(); $before = count($nodes);
$user->setNodes(array_values(array_filter($nodes, static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.' . strtolower($a1) . '.'))));
if(count($user->getNodes()) < $before){ $this->saveAndRefresh($user, $plugin, $sender); $sender->sendMessage(TF::GREEN . 'Unset meta ' . TF::WHITE . $a1 . TF::GREEN . ' from ' . $user->getUsername() . '.'); }
else $sender->sendMessage(TF::YELLOW . 'Meta key ' . TF::WHITE . $a1 . TF::YELLOW . ' not found.');
break;
case 'clear':
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.') && !str_starts_with(strtolower($n->getKey()), 'prefix.') && !str_starts_with(strtolower($n->getKey()), 'suffix.'))));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Cleared all meta from ' . $user->getUsername() . '.');
break;
case 'addprefix':
				case 'setprefix':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta ' . $sub . ' <priority> <value>'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0;
if(str_starts_with($sub, 'set')){ $user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'prefix.')))); }
$user->addNode(new NodeEntry('prefix.' . $pri . '.' . $a2, true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set prefix ' . TF::WHITE . $a2 . TF::GREEN . ' (priority ' . $pri . ') for ' . $user->getUsername() . '.');
break;
case 'addsuffix':
				case 'setsuffix':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta ' . $sub . ' <priority> <value>'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0;
if(str_starts_with($sub, 'set')){ $user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'suffix.')))); }
$user->addNode(new NodeEntry('suffix.' . $pri . '.' . $a2, true, [], null));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set suffix ' . TF::WHITE . $a2 . TF::GREEN . ' (priority ' . $pri . ') for ' . $user->getUsername() . '.');
break;
case 'settemp':
if($a1 === null || $a2 === null || $a3 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta settemp <key> <value> <seconds>'); return; }
$dur = is_numeric($a3) ? (int) $a3 : 0;
if($dur <= 0){ $sender->sendMessage(TF::RED . 'Duration must be positive.'); return; }
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.' . strtolower($a1) . '.'))));
$user->addNode(new NodeEntry('meta.' . $a1 . '.' . $a2, true, [], time() + $dur));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set temp meta ' . TF::WHITE . $a1 . TF::GREEN . ' = ' . TF::WHITE . $a2 . TF::GREEN . " ({$dur}s) for " . $user->getUsername() . '.');
break;
case 'unsettemp':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta unsettemp <key>'); return; }
$bfMt = count($user->getNodes());
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.' . strtolower($a1) . '.') || !$n->isTemporary())));
if(count($user->getNodes()) < $bfMt){ $this->saveAndRefresh($user, $plugin, $sender); $sender->sendMessage(TF::GREEN . 'Removed temp meta ' . TF::WHITE . $a1 . TF::GREEN . ' from ' . $user->getUsername() . '.'); }
else $sender->sendMessage(TF::YELLOW . 'No matching temporary meta found.');
break;
case 'removeprefix':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta removeprefix <priority> [prefix]'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0; $pfxStr = 'prefix.' . $pri . '.';
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), strtolower($pfxStr)) || ($a2 !== null && strtolower(substr($n->getKey(), strlen($pfxStr))) !== strtolower($a2)))));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Removed prefix (priority ' . $pri . ') from ' . $user->getUsername() . '.');
break;
case 'removesuffix':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta removesuffix <priority> [suffix]'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0; $sfxStr = 'suffix.' . $pri . '.';
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), strtolower($sfxStr)) || ($a2 !== null && strtolower(substr($n->getKey(), strlen($sfxStr))) !== strtolower($a2)))));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Removed suffix (priority ' . $pri . ') from ' . $user->getUsername() . '.');
break;
case 'addtempprefix':
				case 'settempprefix':
if($a1 === null || $a2 === null || $a3 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta ' . $sub . ' <priority> <prefix> <seconds>'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0; $dur = is_numeric($a3) ? (int) $a3 : 0;
if($dur <= 0){ $sender->sendMessage(TF::RED . 'Duration must be positive.'); return; }
if(str_starts_with($sub, 'set')) $user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'prefix.') || !$n->isTemporary())));
$user->addNode(new NodeEntry('prefix.' . $pri . '.' . $a2, true, [], time() + $dur));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set temp prefix ' . TF::WHITE . $a2 . TF::GREEN . " (priority $pri, {$dur}s) for " . $user->getUsername() . '.');
break;
case 'addtempsuffix':
				case 'settempsuffix':
if($a1 === null || $a2 === null || $a3 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta ' . $sub . ' <priority> <suffix> <seconds>'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0; $dur = is_numeric($a3) ? (int) $a3 : 0;
if($dur <= 0){ $sender->sendMessage(TF::RED . 'Duration must be positive.'); return; }
if(str_starts_with($sub, 'set')) $user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'suffix.') || !$n->isTemporary())));
$user->addNode(new NodeEntry('suffix.' . $pri . '.' . $a2, true, [], time() + $dur));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Set temp suffix ' . TF::WHITE . $a2 . TF::GREEN . " (priority $pri, {$dur}s) for " . $user->getUsername() . '.');
break;
case 'removetempprefix':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta removetempprefix <priority> [prefix]'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0; $pfxStr2 = 'prefix.' . $pri . '.';
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !$n->isTemporary() || !str_starts_with(strtolower($n->getKey()), strtolower($pfxStr2)) || ($a2 !== null && strtolower(substr($n->getKey(), strlen($pfxStr2))) !== strtolower($a2)))));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Removed temp prefix (priority ' . $pri . ') from ' . $user->getUsername() . '.');
break;
case 'removetempsuffix':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' user ' . $un . ' meta removetempsuffix <priority> [suffix]'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0; $sfxStr2 = 'suffix.' . $pri . '.';
$user->setNodes(array_values(array_filter($user->getNodes(), static fn(NodeEntry $n) => !$n->isTemporary() || !str_starts_with(strtolower($n->getKey()), strtolower($sfxStr2)) || ($a2 !== null && strtolower(substr($n->getKey(), strlen($sfxStr2))) !== strtolower($a2)))));
$this->saveAndRefresh($user, $plugin, $sender);
$sender->sendMessage(TF::GREEN . 'Removed temp suffix (priority ' . $pri . ') from ' . $user->getUsername() . '.');
break;
default:
$sender->sendMessage(TF::RED . "Unknown: 'meta $sub'. Use: info, set, unset, settemp, unsettemp, clear, addprefix, addsuffix, setprefix, setsuffix, removeprefix, removesuffix, addtempprefix, addtempsuffix, settempprefix, settempsuffix, removetempprefix, removetempsuffix");
}
}
}
