<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\group;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
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
use function strtolower;
use function substr;
use function time;

class GroupParentCommand extends BaseSubCommand{

protected function prepare() : void{
$this->setPermission('luckperms.command');
$this->registerArgument(0, new RawStringArgument('group', true));
$this->registerArgument(1, new RawStringArgument('action', true));
$this->registerArgument(2, new RawStringArgument('sub', true));
$this->registerArgument(3, new RawStringArgument('arg1', true));
$this->registerArgument(4, new RawStringArgument('arg2', true));
$this->registerArgument(5, new RawStringArgument('arg3', true));
}

public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
$plugin = LuckPerms::getInstance();
$groupName = (string) ($args['group'] ?? '');
$action = strtolower((string) ($args['action'] ?? ''));
$sub = strtolower((string) ($args['sub'] ?? ''));
$arg1 = isset($args['arg1']) ? (string) $args['arg1'] : null;
$arg2 = isset($args['arg2']) ? (string) $args['arg2'] : null;
$arg3 = isset($args['arg3']) ? (string) $args['arg3'] : null;

if($groupName === ''){
$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' group <group> <action> ...');
return;
}

$group = $plugin->getGroupManager()->getIfLoaded(strtolower($groupName));
if($group === null){
$group = $plugin->getStorage()->loadGroup(strtolower($groupName));
}
if($group === null){
$sender->sendMessage(TF::RED . "Group '$groupName' not found. Use /lp creategroup <name> to create it.");
return;
}

switch($action){
case '':
				case 'info':
$this->cmdInfo($sender, $group, $plugin); break;
case 'editor':
$sender->sendMessage(TF::YELLOW . 'Use ' . TF::WHITE . '/lp editor' . TF::YELLOW . ' for a full web editor session.'); break;
case 'listmembers':
$this->cmdListMembers($sender, $group, $plugin); break;
case 'showtracks':
$this->cmdShowTracks($sender, $group, $plugin); break;
case 'setweight':
if($arg1 === null || !is_numeric($arg1)){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' group ' . $groupName . ' setweight <number>'); return; }
$group->setWeight((int) $arg1);
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . 'Set weight of ' . TF::WHITE . $group->getName() . TF::GREEN . ' to ' . $arg1 . '.'); break;
case 'setdisplayname':
if($arg1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' group ' . $groupName . ' setdisplayname <name>'); return; }
$group->setDisplayName($arg1);
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . 'Set display name of ' . TF::WHITE . $group->getName() . TF::GREEN . ' to ' . $arg1 . '.'); break;
case 'rename':
if($sub === ''){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' group ' . $groupName . ' rename <new-name>'); return; }
$existing = $plugin->getGroupManager()->getIfLoaded(strtolower($sub));
if($existing !== null){ $sender->sendMessage(TF::RED . "Group '$sub' already exists."); return; }
$newGroup = $plugin->getGroupManager()->getOrMake($sub);
$newGroup->setNodes($group->getNodes());
$newGroup->setWeight($group->getWeight());
$plugin->getStorage()->saveGroup($newGroup);
$plugin->getStorage()->deleteGroup($group->getName());
$this->refreshAll($plugin);
$sender->sendMessage(TF::GREEN . 'Renamed group ' . TF::WHITE . $group->getName() . TF::GREEN . ' to ' . TF::WHITE . $sub . TF::GREEN . '.'); break;
case 'clone':
if($sub === ''){ $sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' group ' . $groupName . ' clone <new-name>'); return; }
$clone = $plugin->getGroupManager()->getOrMake($sub);
$clone->setNodes($group->getNodes());
$clone->setWeight($group->getWeight());
$plugin->getStorage()->saveGroup($clone);
$sender->sendMessage(TF::GREEN . 'Cloned group ' . TF::WHITE . $group->getName() . TF::GREEN . ' to ' . TF::WHITE . $sub . TF::GREEN . '.'); break;
case 'clear':
$count = count($group->getNodes()); $group->setNodes([]);
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Cleared $count node(s) from group " . $group->getName() . '.'); break;
case 'permission':
$this->handlePermission($sender, $aliasUsed, $groupName, $group, $sub, $arg1, $arg2, $arg3, $plugin); break;
case 'parent':
$this->handleParent($sender, $aliasUsed, $groupName, $group, $sub, $arg1, $arg2, $plugin); break;
case 'meta':
$this->handleMeta($sender, $aliasUsed, $groupName, $group, $sub, $arg1, $arg2, $arg3, $plugin); break;
default:
$sender->sendMessage(TF::RED . "Unknown action '$action'. Use: info, editor, listmembers, showtracks, setweight, setdisplayname, rename, clone, clear, permission, parent, meta");
}
}

private function saveAndRefresh(Group $group, LuckPerms $plugin) : void{
$plugin->getStorage()->saveGroup($group);
PermissionHelper::refreshAll($plugin);
}

private function refreshAll(LuckPerms $plugin) : void{
PermissionHelper::refreshAll($plugin);
}

private function removeNodeByKey(Group $group, string $key) : bool{
$key = strtolower($key);
$nodes = $group->getNodes();
$new = array_values(array_filter($nodes, static fn(NodeEntry $n) => strtolower($n->getKey()) !== $key));
$group->setNodes($new);
return count($new) < count($nodes);
}

private function cmdInfo(CommandSender $sender, Group $group, LuckPerms $plugin) : void{
$nodes = $group->getNodes();
$parents = array_filter($nodes, static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.'));
$perms = array_filter($nodes, static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.'));
$sender->sendMessage(TF::GOLD . '=== Group Info: ' . TF::WHITE . $group->getName() . TF::GOLD . ' ===');
$sender->sendMessage(TF::YELLOW . 'Weight: ' . TF::WHITE . $group->getWeight());
if($group->getDisplayName() !== null)
$sender->sendMessage(TF::YELLOW . 'Display name: ' . TF::WHITE . $group->getDisplayName());
$sender->sendMessage(TF::YELLOW . 'Permissions: ' . TF::WHITE . count($perms) . ' node(s)');
if(count($parents) > 0)
$sender->sendMessage(TF::YELLOW . 'Parent groups: ' . TF::WHITE . implode(', ', array_map(static fn(NodeEntry $n) => substr($n->getKey(), 6), $parents)));
}

private function cmdListMembers(CommandSender $sender, Group $group, LuckPerms $plugin) : void{
$members = [];
foreach($plugin->getUserManager()->getAll() as $user){
foreach($user->getNodes() as $node){
if(strtolower($node->getKey()) === 'group.' . strtolower($group->getName())){
$members[] = $user->getUsername(); break;
}
}
}
if(count($members) === 0){ $sender->sendMessage(TF::YELLOW . 'No members in group ' . $group->getName() . '.'); return; }
$sender->sendMessage(TF::GOLD . 'Members of ' . $group->getName() . ': ' . TF::WHITE . implode(', ', $members));
}

private function cmdShowTracks(CommandSender $sender, Group $group, LuckPerms $plugin) : void{
$found = false;
foreach($plugin->getTrackManager()->getAll() as $track){
if(in_array(strtolower($group->getName()), array_map('strtolower', $track->getGroups()), true)){
$rendered = array_map(static fn(string $g) => (strtolower($g) === strtolower($group->getName()) ? TF::GREEN : TF::GRAY) . $g . TF::RESET, $track->getGroups());
$sender->sendMessage(TF::YELLOW . $track->getName() . ': ' . implode(TF::WHITE . ' > ', $rendered));
$found = true;
}
}
if(!$found) $sender->sendMessage(TF::YELLOW . 'Group ' . $group->getName() . ' is not part of any track.');
}

/* ─── permission ─── */
private function handlePermission(CommandSender $sender, string $al, string $gn, Group $group, string $sub, ?string $a1, ?string $a2, ?string $a3, LuckPerms $plugin) : void{
switch($sub){
case '':
				case 'info':
$page = is_numeric($a1) ? (int) $a1 : 1;
$nodes = array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.')));
if(count($nodes) === 0){ $sender->sendMessage(TF::YELLOW . "Group $gn has no permission nodes."); return; }
$perPage = 12; $pages = (int) ceil(count($nodes) / $perPage); $page = max(1, min($page, $pages));
$sender->sendMessage(TF::GOLD . "--- Permissions ($gn) [$page/$pages] ---");
foreach(array_slice($nodes, ($page - 1) * $perPage, $perPage) as $n)
$sender->sendMessage(($n->getValue() ? TF::GREEN : TF::RED) . '  ' . $n->getKey() . ($n->isTemporary() ? TF::GRAY . ' (' . date('Y-m-d H:i', $n->getExpiry() ?? 0) . ')' : ''));
break;
case 'set':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' permission set <node> [true|false]'); return; }
$val = ($a2 === null || strtolower($a2) !== 'false');
$this->removeNodeByKey($group, $a1);
$group->addNode(new NodeEntry($a1, $val, [], null));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . 'Set ' . TF::WHITE . $a1 . TF::GREEN . ' = ' . ($val ? 'true' : 'false') . " for group $gn.");
break;
case 'unset':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' permission unset <node>'); return; }
if($this->removeNodeByKey($group, $a1)){ $this->saveAndRefresh($group, $plugin); $sender->sendMessage(TF::GREEN . 'Unset ' . TF::WHITE . $a1 . TF::GREEN . " from group $gn."); }
else $sender->sendMessage(TF::YELLOW . 'Node ' . TF::WHITE . $a1 . TF::YELLOW . " not found on group $gn.");
break;
case 'settemp':
if($a1 === null || $a2 === null || $a3 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' permission settemp <node> <true|false> <seconds>'); return; }
$val = strtolower($a2) !== 'false'; $dur = is_numeric($a3) ? (int) $a3 : 0;
if($dur <= 0){ $sender->sendMessage(TF::RED . 'Duration must be positive.'); return; }
$this->removeNodeByKey($group, $a1);
$group->addNode(new NodeEntry($a1, $val, [], time() + $dur));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . 'Set temp ' . TF::WHITE . $a1 . TF::GREEN . " ($dur s) for group $gn.");
break;
case 'unsettemp':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' permission unsettemp <node>'); return; }
$nodes = $group->getNodes(); $before = count($nodes);
$group->setNodes(array_values(array_filter($nodes, static fn(NodeEntry $n) => strtolower($n->getKey()) !== strtolower($a1) || !$n->isTemporary())));
if(count($group->getNodes()) < $before){ $this->saveAndRefresh($group, $plugin); $sender->sendMessage(TF::GREEN . 'Removed temp node ' . TF::WHITE . $a1 . TF::GREEN . " from group $gn."); }
else $sender->sendMessage(TF::YELLOW . 'No matching temporary node found.');
break;
case 'check':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' permission check <node>'); return; }
$eff = PermissionHelper::resolveGroupPermissions($gn, $plugin);
$has = $eff[strtolower($a1)] ?? false;
$sender->sendMessage(TF::YELLOW . "Group $gn -> $a1: " . ($has ? TF::GREEN . 'true' : TF::RED . 'false'));
break;
case 'clear':
$group->setNodes(array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.'))));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Cleared permission nodes from group $gn.");
break;
default:
$sender->sendMessage(TF::RED . "Unknown: 'permission $sub'. Use: info, set, unset, settemp, unsettemp, check, clear");
}
}

/* ─── parent ─── */
private function handleParent(CommandSender $sender, string $al, string $gn, Group $group, string $sub, ?string $a1, ?string $a2, LuckPerms $plugin) : void{
switch($sub){
case '':
				case 'info':
$page = is_numeric($a1) ? (int) $a1 : 1;
$nodes = array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'group.')));
if(count($nodes) === 0){ $sender->sendMessage(TF::YELLOW . "Group $gn has no parent groups."); return; }
$perPage = 12; $pages = (int) ceil(count($nodes) / $perPage); $page = max(1, min($page, $pages));
$sender->sendMessage(TF::GOLD . "--- Parents ($gn) [$page/$pages] ---");
foreach(array_slice($nodes, ($page - 1) * $perPage, $perPage) as $n)
$sender->sendMessage(TF::GREEN . '  - ' . substr($n->getKey(), 6) . ($n->isTemporary() ? TF::GRAY . ' (' . date('Y-m-d H:i', $n->getExpiry() ?? 0) . ')' : ''));
break;
case 'add':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' parent add <group>'); return; }
$plugin->getGroupManager()->getOrMake($a1);
$nk = 'group.' . strtolower($a1);
$this->removeNodeByKey($group, $nk);
$group->addNode(new NodeEntry($nk, true, [], null));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Added group $gn to parent group " . TF::WHITE . $a1 . TF::GREEN . '.');
break;
case 'remove':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' parent remove <group>'); return; }
if($this->removeNodeByKey($group, 'group.' . strtolower($a1))){ $this->saveAndRefresh($group, $plugin); $sender->sendMessage(TF::GREEN . "Removed parent group " . TF::WHITE . $a1 . TF::GREEN . " from $gn."); }
else $sender->sendMessage(TF::YELLOW . "Group $gn does not inherit from " . TF::WHITE . $a1 . TF::YELLOW . '.');
break;
case 'addtemp':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' parent addtemp <group> <seconds>'); return; }
$dur = is_numeric($a2) ? (int) $a2 : 0;
if($dur <= 0){ $sender->sendMessage(TF::RED . 'Duration must be positive.'); return; }
$plugin->getGroupManager()->getOrMake($a1);
$nk = 'group.' . strtolower($a1);
$this->removeNodeByKey($group, $nk);
$group->addNode(new NodeEntry($nk, true, [], time() + $dur));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Added temp parent $a1 to group $gn ($dur s).");
break;
case 'removetemp':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' parent removetemp <group>'); return; }
$nk = 'group.' . strtolower($a1); $nodes = $group->getNodes(); $before = count($nodes);
$group->setNodes(array_values(array_filter($nodes, static fn(NodeEntry $n) => strtolower($n->getKey()) !== $nk || !$n->isTemporary())));
if(count($group->getNodes()) < $before){ $this->saveAndRefresh($group, $plugin); $sender->sendMessage(TF::GREEN . "Removed temp parent $a1 from group $gn."); }
else $sender->sendMessage(TF::YELLOW . 'No matching temporary parent found.');
break;
case 'clear':
$group->setNodes(array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'group.'))));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Cleared all parent groups from group $gn.");
break;
default:
$sender->sendMessage(TF::RED . "Unknown: 'parent $sub'. Use: info, add, remove, addtemp, removetemp, clear");
}
}

/* ─── meta ─── */
private function handleMeta(CommandSender $sender, string $al, string $gn, Group $group, string $sub, ?string $a1, ?string $a2, ?string $a3, LuckPerms $plugin) : void{
switch($sub){
case '':
				case 'info':
$nodes = array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => str_starts_with(strtolower($n->getKey()), 'meta.') || str_starts_with(strtolower($n->getKey()), 'prefix.') || str_starts_with(strtolower($n->getKey()), 'suffix.')));
if(count($nodes) === 0){ $sender->sendMessage(TF::YELLOW . "Group $gn has no meta."); return; }
$sender->sendMessage(TF::GOLD . "--- Meta ($gn) ---");
foreach($nodes as $n) $sender->sendMessage(TF::YELLOW . '  - ' . TF::WHITE . $n->getKey());
break;
case 'set':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' meta set <key> <value>'); return; }
$group->setNodes(array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.' . strtolower($a1) . '.'))));
$group->addNode(new NodeEntry('meta.' . $a1 . '.' . $a2, true, [], null));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Set meta $a1 = $a2 for group $gn.");
break;
case 'unset':
if($a1 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' meta unset <key>'); return; }
$nodes = $group->getNodes(); $before = count($nodes);
$group->setNodes(array_values(array_filter($nodes, static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.' . strtolower($a1) . '.'))));
if(count($group->getNodes()) < $before){ $this->saveAndRefresh($group, $plugin); $sender->sendMessage(TF::GREEN . "Unset meta $a1 from group $gn."); }
else $sender->sendMessage(TF::YELLOW . "Meta key $a1 not found on group $gn.");
break;
case 'clear':
$group->setNodes(array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'meta.') && !str_starts_with(strtolower($n->getKey()), 'prefix.') && !str_starts_with(strtolower($n->getKey()), 'suffix.'))));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Cleared all meta from group $gn.");
break;
case 'addprefix':
				case 'setprefix':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' meta ' . $sub . ' <priority> <value>'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0;
if(str_starts_with($sub, 'set')) $group->setNodes(array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'prefix.'))));
$group->addNode(new NodeEntry('prefix.' . $pri . '.' . $a2, true, [], null));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Set prefix '$a2' (priority $pri) for group $gn.");
break;
case 'addsuffix':
				case 'setsuffix':
if($a1 === null || $a2 === null){ $sender->sendMessage(TF::RED . 'Usage: /' . $al . ' group ' . $gn . ' meta ' . $sub . ' <priority> <value>'); return; }
$pri = is_numeric($a1) ? (int) $a1 : 0;
if(str_starts_with($sub, 'set')) $group->setNodes(array_values(array_filter($group->getNodes(), static fn(NodeEntry $n) => !str_starts_with(strtolower($n->getKey()), 'suffix.'))));
$group->addNode(new NodeEntry('suffix.' . $pri . '.' . $a2, true, [], null));
$this->saveAndRefresh($group, $plugin);
$sender->sendMessage(TF::GREEN . "Set suffix '$a2' (priority $pri) for group $gn.");
break;
default:
$sender->sendMessage(TF::RED . "Unknown: 'meta $sub'. Use: info, set, unset, clear, addprefix, addsuffix, setprefix, setsuffix");
}
}
}
