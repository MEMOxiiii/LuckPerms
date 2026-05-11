<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\LuckPerms;
use function method_exists;
use function str_starts_with;
use function strtolower;

class ApiNodeMatcherFactory extends ApiAbstractManager{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

public function key(string $key) : mixed{
return fn(mixed $node) : bool => method_exists($node, "getKey") && strtolower($node->getKey()) === strtolower($key);
}

public function keyStartsWith(string $keyPrefix) : mixed{
return fn(mixed $node) : bool => method_exists($node, "getKey") && str_starts_with(strtolower($node->getKey()), strtolower($keyPrefix));
}

public function type(mixed $type) : mixed{
return fn(mixed $node) : bool => $node instanceof $type;
}
}
