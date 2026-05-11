<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\model\user\User as UserInterface;
use jasonw4331\LuckPerms\api\model\user\UserManager as UserManagerInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\User as InternalUser;
use Ramsey\Uuid\UuidInterface;
use function array_map;
use function array_values;

class ApiUserManager extends ApiAbstractManager implements UserManagerInterface{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

private function proxy(InternalUser $user) : ApiUser{
return new ApiUser($this->plugin, $user);
}

public function loadUser(UuidInterface $uniqueId, ?string $username = null) : ?UserInterface{
$user = $this->plugin->getStorage()->loadUser($uniqueId, $username);
return $user !== null ? $this->proxy($user) : null;
}

public function saveUser(UserInterface $user) : void{
if($user instanceof ApiUser){
$this->plugin->getStorage()->saveUser($user->getInternalUser());
}
}

public function getUser(UuidInterface $uniqueId) : ?UserInterface{
$user = $this->plugin->getUserManager()->getIfLoaded($uniqueId);
return $user !== null ? $this->proxy($user) : null;
}

public function isLoaded(UuidInterface $uniqueId) : bool{
return $this->plugin->getUserManager()->getIfLoaded($uniqueId) !== null;
}

public function cleanupUser(UserInterface $user) : void{
// cleanup cached data if no longer online
}

public function getLoadedUsers() : array{
return array_values(array_map(
fn(InternalUser $u) => $this->proxy($u),
$this->plugin->getUserManager()->getAll()
));
}

public function lookupUniqueId(string $username) : ?UuidInterface{
return $this->plugin->getStorage()->getPlayerUniqueId($username);
}

public function lookupUsername(UuidInterface $uniqueId) : ?string{
return $this->plugin->getStorage()->getPlayerName($uniqueId);
}
}
