<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

use jasonw4331\LuckPerms\api\query\QueryMode;
use jasonw4331\LuckPerms\api\query\QueryOptions;
use jasonw4331\LuckPerms\api\query\QueryOptionsBuilder;

/**
 * Manages contextual data for players and provides utility methods.
 */
interface ContextManager
{
	public function getContext(object $subject) : ImmutableContextSet;

	public function getContextForUser(\jasonw4331\LuckPerms\api\model\user\User $user) : ?ImmutableContextSet;

	public function getStaticContext() : ImmutableContextSet;

	public function queryOptionsBuilder(QueryMode $mode) : QueryOptionsBuilder;

	public function getQueryOptions(object $subject) : QueryOptions;

	public function getQueryOptionsForUser(\jasonw4331\LuckPerms\api\model\user\User $user) : ?QueryOptions;

	public function getStaticQueryOptions() : QueryOptions;

	public function registerCalculator(ContextCalculator $calculator) : void;

	public function unregisterCalculator(ContextCalculator $calculator) : void;

	public function signalContextUpdate(object $subject) : void;

	public function getContextSetFactory() : ContextSetFactory;
}
