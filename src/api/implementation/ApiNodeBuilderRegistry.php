<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\node\factory\NodeFactory;

class ApiNodeBuilderRegistry extends ApiAbstractManager{
private NodeFactory $factory;

public function __construct(LuckPerms $plugin, NodeFactory $factory){
parent::__construct($plugin);
$this->factory = $factory;
}

public function forPermission() : mixed{
return $this->factory->permission();
}

public function forRegexPermission() : mixed{
return $this->factory->regexPermission();
}

public function forInheritance() : mixed{
return $this->factory->inheritance();
}

public function forPrefix() : mixed{
return $this->factory->prefix();
}

public function forSuffix() : mixed{
return $this->factory->suffix();
}

public function forMeta() : mixed{
return $this->factory->meta();
}

public function forWeight() : mixed{
return $this->factory->weight();
}

public function forDisplayName() : mixed{
return $this->factory->displayName();
}
}
