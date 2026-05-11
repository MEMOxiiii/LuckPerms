<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\model\user\User as UserInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\User as InternalUser;
use Ramsey\Uuid\UuidInterface;

class ApiUser extends ApiPermissionHolder implements UserInterface{
private InternalUser $handle;

public function __construct(LuckPerms $plugin, InternalUser $handle){
parent::__construct($plugin, $handle);
$this->handle = $handle;
}

public function getInternalUser() : InternalUser{
return $this->handle;
}

public function getUniqueId() : UuidInterface{
return $this->handle->getUniqueId();
}

public function getUsername() : ?string{
return $this->handle->getUsername();
}

public function getPrimaryGroup() : string{
return $this->handle->getCachedData()->getMetaData()->getPrimaryGroup() ?? 'default';
}

public function setPrimaryGroup(string $group) : bool{
$this->handle->getPrimaryGroup()->setStoredValue($group);
return true;
}

public function getCachedData() : mixed{
return $this->handle->getCachedData();
}
}
