<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\api\implementation\ApiContextSetFactory;
use jasonw4331\LuckPerms\api\query\QueryOptions;
use jasonw4331\LuckPerms\query\QueryOptionsImpl;

class ContextManager{
	private array $calculators = [];
	private ApiContextSetFactory $factory;

	public function __construct(LuckPerms $plugin){
		$this->factory = new ApiContextSetFactory();
	}

	public function registerCalculator(object $calculator) : void{
		$this->calculators[] = $calculator;
	}

	public function getQueryOptions(mixed $subject = null) : QueryOptions{
		return new QueryOptionsImpl();
	}

	public function getContextSetFactory() : ApiContextSetFactory{
		return $this->factory;
	}
}
