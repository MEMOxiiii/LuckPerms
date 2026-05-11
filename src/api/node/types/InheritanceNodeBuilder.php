<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\node\types;

use jasonw4331\LuckPerms\api\Group;
use jasonw4331\LuckPerms\api\node\NodeBuilder;

abstract class InheritanceNodeBuilder extends NodeBuilder{

	abstract function group(string|Group $group) : InheritanceNodeBuilder;

}
