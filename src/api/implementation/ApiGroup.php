<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\model\group\Group as GroupInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group as InternalGroup;

class ApiGroup extends ApiPermissionHolder implements GroupInterface{
private InternalGroup $handle;

public function __construct(LuckPerms $plugin, InternalGroup $handle){
parent::__construct($plugin, $handle);
$this->handle = $handle;
}

public function getInternalGroup() : InternalGroup{
return $this->handle;
}

public function getName() : string{
return $this->handle->getName();
}

public function getDisplayName() : ?string{
return $this->handle->getDisplayName();
}

public function getWeight() : int{
return $this->handle->getWeight();
}

public function getCachedData() : mixed{
return $this->handle->getCachedData();
}
}
