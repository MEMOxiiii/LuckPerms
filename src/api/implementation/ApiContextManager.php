<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\context\ContextManager;
use jasonw4331\LuckPerms\context\ContextManager as InternalContextManager;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\player\Player;

class ApiContextManager extends ApiAbstractManager implements ContextManager{
private InternalContextManager $contextManager;

public function __construct(LuckPerms $plugin, InternalContextManager $contextManager){
parent::__construct($plugin);
$this->contextManager = $contextManager;
}

public function getQueryOptions(?Player $player = null) : mixed{
if($player !== null){
return $this->contextManager->getQueryOptions($player);
}
return $this->contextManager->getStaticQueryOptions();
}

public function getContext(?Player $player = null) : mixed{
return $this->contextManager->getContext($player);
}

public function getStaticContext() : mixed{
return $this->contextManager->getStaticContext();
}

public function registerCalculator(mixed $calculator) : void{
$this->contextManager->registerCalculator($calculator);
}

public function unregisterCalculator(mixed $calculator) : void{
$this->contextManager->unregisterCalculator($calculator);
}

public function getContextSetFactory() : mixed{
return $this->contextManager->getContextSetFactory();
}

public function queryOptionsBuilder(mixed $mode) : mixed{
return $this->plugin->getContextManager()->queryOptionsBuilder($mode);
}
}
