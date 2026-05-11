<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\model\user\User as UserInterface;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\player\Player;

class ApiPlayerAdapter extends ApiAbstractManager{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

public function getUser(Player $player) : ?UserInterface{
$user = $this->plugin->getUserManager()->getIfLoaded($player->getUniqueId());
if($user === null) return null;
return new ApiUser($this->plugin, $user);
}

public function getPlayer(UserInterface $user) : ?Player{
if($user instanceof ApiUser){
return $this->plugin->getServer()->getPlayerByUUID($user->getUniqueId());
}
return null;
}
}
