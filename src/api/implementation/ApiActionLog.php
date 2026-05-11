<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\actionlog\Log;
use jasonw4331\LuckPerms\api\actionlog\ActionLog;
use jasonw4331\LuckPerms\LuckPerms;
use const PHP_INT_MAX;

class ApiActionLog extends ApiAbstractManager implements ActionLog{
private Log $handle;

public function __construct(LuckPerms $plugin, Log $handle){
parent::__construct($plugin);
$this->handle = $handle;
}

public function getRecentActions(int $pageSize = 15, int $page = 1) : array{
return $this->handle->getContent($pageSize, $page);
}

public function getActions() : array{
return $this->handle->getContent(PHP_INT_MAX, 1);
}
}
