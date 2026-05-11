<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Accepts context entries from a ContextCalculator.
 */
interface ContextConsumer
{
	public function accept(string $key, string $value) : void;
}
