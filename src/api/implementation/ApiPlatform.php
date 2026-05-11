<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\LuckPerms;
use function count;

class ApiPlatform extends ApiAbstractManager{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

public function getPlatformType() : string{
return 'PocketMine-MP';
}

public function getApiVersion() : string{
return $this->plugin->getDescription()->getVersion();
}

public function getServerVersion() : string{
return $this->plugin->getServer()->getVersion();
}

public function getUniqueConnections() : int{
return count($this->plugin->getServer()->getOnlinePlayers());
}

public function getName() : string{
return 'pocketmine';
}
}
