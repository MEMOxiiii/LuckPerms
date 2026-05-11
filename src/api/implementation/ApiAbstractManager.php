<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\LuckPerms;

abstract class ApiAbstractManager{
protected LuckPerms $plugin;

public function __construct(LuckPerms $plugin){
$this->plugin = $plugin;
}
}
