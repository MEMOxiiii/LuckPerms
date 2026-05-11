<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context\manager;

use jasonw4331\LuckPerms\api\implementation\ApiContextSetFactory;
use jasonw4331\LuckPerms\context\ImmutableContextSetImpl;
use jasonw4331\LuckPerms\query\QueryOptions;
use function array_filter;
use function array_values;
use function method_exists;

/**
 * Base implementation of context manager functionality.
 * Manages context calculators and builds QueryOptions for subjects.
 */
abstract class ContextManagerBase{
	/** @var object[] registered context calculators */
	private array $calculators = [];
	private ApiContextSetFactory $factory;

	public function __construct(){
		$this->factory = new ApiContextSetFactory();
	}

	/**
	 * Register a new context calculator.
	 */
	public function registerCalculator(object $calculator) : void{
		$this->calculators[] = $calculator;
	}

	/**
	 * Unregister a context calculator.
	 */
	public function unregisterCalculator(object $calculator) : void{
		$this->calculators = array_filter($this->calculators, static fn(object $c) => $c !== $calculator);
	}

	/** @return object[] */
	public function getCalculators() : array{
		return array_values($this->calculators);
	}

	public function getContextSetFactory() : ApiContextSetFactory{
		return $this->factory;
	}

	/**
	 * Build a context set for the given subject by running all calculators.
	 */
	public function buildContexts(mixed $subject) : ImmutableContextSetImpl{
		$contexts = [];
		foreach($this->calculators as $calculator){
			if(method_exists($calculator, 'giveApplicableContext')){
				$calculator->giveApplicableContext($subject, $contexts);
			}
		}
		return $this->factory->makeImmutableContextSet($contexts);
	}

	/**
	 * Returns the {@link QueryOptions} for the given subject.
	 */
	abstract public function getQueryOptions(mixed $subject = null) : QueryOptions;
}
