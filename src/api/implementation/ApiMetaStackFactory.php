<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\metastacking\MetaStackDefinition;
use jasonw4331\LuckPerms\api\metastacking\MetaStackElement;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\metastacking\SimpleMetaStackDefinition;
use jasonw4331\LuckPerms\metastacking\StandardStackElements;
use function array_map;

class ApiMetaStackFactory extends ApiAbstractManager{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

public function fromString(string $definition) : MetaStackDefinition{
$elements = [];
foreach(explode(',', $definition) as $part){
$part = trim($part);
if($part !== ''){
$elem = StandardStackElements::parseFromString($part);
if($elem !== null) $elements[] = $elem;
}
}
return new SimpleMetaStackDefinition($elements, null, null, null);
}

public function metaStackDefinition(array $elements, mixed $duplicateRemovalFunction, string $startSpacer = '', string $middleSpacer = ' ', string $endSpacer = '') : MetaStackDefinition{
return new SimpleMetaStackDefinition($elements, $duplicateRemovalFunction, $startSpacer, $endSpacer, $middleSpacer);
}
}
