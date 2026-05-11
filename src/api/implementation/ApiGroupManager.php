<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\model\group\Group as GroupInterface;
use jasonw4331\LuckPerms\api\model\group\GroupManager as GroupManagerInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group as InternalGroup;
use function array_map;
use function array_values;
use function strtolower;

class ApiGroupManager extends ApiAbstractManager implements GroupManagerInterface{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

private function proxy(InternalGroup $group) : ApiGroup{
return new ApiGroup($this->plugin, $group);
}

public function createAndLoadGroup(string $name) : ?GroupInterface{
$group = $this->plugin->getGroupManager()->getOrMake(strtolower($name));
return $this->proxy($group);
}

public function loadGroup(string $name) : ?GroupInterface{
$group = $this->plugin->getGroupManager()->getIfLoaded(strtolower($name));
return $group !== null ? $this->proxy($group) : null;
}

public function saveGroup(GroupInterface $group) : void{
if($group instanceof ApiGroup){
$this->plugin->getStorage()->saveGroup($group->getInternalGroup());
}
}

public function deleteGroup(GroupInterface $group) : void{
if($group instanceof ApiGroup){
$this->plugin->getGroupManager()->unload($group->getName());
}
}

public function loadAllGroups() : void{
$this->plugin->getStorage()->loadAllGroups();
}

public function getGroup(string $name) : ?GroupInterface{
$group = $this->plugin->getGroupManager()->getIfLoaded(strtolower($name));
return $group !== null ? $this->proxy($group) : null;
}

public function isLoaded(string $name) : bool{
return $this->plugin->getGroupManager()->getIfLoaded(strtolower($name)) !== null;
}

public function getLoadedGroups() : array{
return array_values(array_map(
fn(InternalGroup $g) => $this->proxy($g),
$this->plugin->getGroupManager()->getAll()
));
}
}
