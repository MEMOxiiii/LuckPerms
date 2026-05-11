<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\track\Track as TrackInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Track as InternalTrack;

class ApiTrack extends ApiAbstractManager implements TrackInterface{
private InternalTrack $handle;

public function __construct(LuckPerms $plugin, InternalTrack $handle){
parent::__construct($plugin);
$this->handle = $handle;
}

public function getInternalTrack() : InternalTrack{
return $this->handle;
}

public function getName() : string{
return $this->handle->getName();
}

public function getGroups() : array{
return $this->handle->getGroups();
}

public function getSize() : int{
return count($this->handle->getGroups());
}

public function getPrevious(string $group) : ?string{
$groups = $this->handle->getGroups();
$idx = $this->handle->indexOf($group);
return $idx > 0 ? $groups[$idx - 1] : null;
}

public function getNext(string $group) : ?string{
$groups = $this->handle->getGroups();
$idx = $this->handle->indexOf($group);
return ($idx >= 0 && $idx < count($groups) - 1) ? $groups[$idx + 1] : null;
}

public function appendGroup(string $group) : void{
$this->handle->appendGroup($group);
}

public function insertGroup(string $group, int $position) : void{
$this->handle->insertGroup($group, $position);
}

public function removeGroup(string $group) : void{
$this->handle->removeGroup($group);
}

public function containsGroup(string $group) : bool{
return $this->handle->containsGroup($group);
}
}
