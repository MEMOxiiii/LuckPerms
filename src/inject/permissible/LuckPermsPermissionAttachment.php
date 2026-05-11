<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\permissible;

use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;

class LuckPermsPermissionAttachment{
private PermissionAttachment $attachment;

public function __construct(Player $player){
$this->attachment = $player->addAttachment($player->getServer()->getPluginManager()->getPlugin('LuckPerms'));
}

public function setPermission(string $permission, bool $value) : void{
$this->attachment->setPermission($permission, $value);
}

public function clearPermissions() : void{
$perms = $this->attachment->getPermissions();
foreach($perms as $perm => $_){
$this->attachment->unsetPermission($perm);
}
}

public function getAttachment() : PermissionAttachment{
return $this->attachment;
}
}
