<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\query\QueryOptionsImpl;

class ApiQueryOptionsRegistry extends ApiAbstractManager{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

public function defaultNonContextualOptions() : mixed{
return QueryOptionsImpl::DEFAULT_NON_CONTEXTUAL;
}

public function defaultContextualOptions() : mixed{
return QueryOptionsImpl::DEFAULT_CONTEXTUAL;
}
}
