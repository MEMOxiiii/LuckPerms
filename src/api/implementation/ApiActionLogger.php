<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\actionlog\LogDispatcher;
use jasonw4331\LuckPerms\api\actionlog\ActionLogger;
use jasonw4331\LuckPerms\LuckPerms;

class ApiActionLogger extends ApiAbstractManager implements ActionLogger{
private LogDispatcher $dispatcher;

public function __construct(LuckPerms $plugin, LogDispatcher $dispatcher){
parent::__construct($plugin);
$this->dispatcher = $dispatcher;
}

public function submit(mixed $action) : void{
$this->dispatcher->dispatchFromApi($action);
}

public function broadcastAction(mixed $action) : void{
$this->dispatcher->broadcastAction($action);
}
}
