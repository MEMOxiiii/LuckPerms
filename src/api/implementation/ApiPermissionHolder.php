<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\model\PermissionHolder as PermissionHolderInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\PermissionHolder as InternalHolder;
use jasonw4331\LuckPerms\node\NodeEntry;
use function array_map;
use function array_values;

abstract class ApiPermissionHolder implements PermissionHolderInterface{
protected LuckPerms $plugin;
protected InternalHolder $handle;

public function __construct(LuckPerms $plugin, InternalHolder $handle){
$this->plugin = $plugin;
$this->handle = $handle;
}

public function getFriendlyName() : string{
return $this->handle->getPlainDisplayName();
}

public function getNodes() : array{
return $this->handle->getNodes();
}

public function getDistinctNodes() : array{
$seen = [];
$result = [];
foreach($this->handle->getNodes() as $node){
$key = $node->getKey() . '|' . ($node->getValue() ? 'true' : 'false');
if(!isset($seen[$key])){
$seen[$key] = true;
$result[] = $node;
}
}
return $result;
}

public function addNode(mixed $node) : mixed{
if($node instanceof NodeEntry){
$this->handle->addNode($node);
return true;
}
return false;
}

public function removeNode(mixed $node) : mixed{
if($node instanceof NodeEntry){
$this->handle->removeNode($node);
return true;
}
return false;
}

public function clearNodes() : void{
$this->handle->clearNodes();
}

public function auditTemporaryNodes() : void{
$this->handle->auditTemporaryNodes();
}

public function getCachedData() : mixed{
return $this->handle->getCachedData();
}

public function getOwnNodes() : array{
return $this->handle->getNodes();
}
}
