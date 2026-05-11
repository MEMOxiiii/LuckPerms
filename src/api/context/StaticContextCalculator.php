<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Calculates static (non-subject) contexts.
 */
interface StaticContextCalculator extends ContextCalculator
{
	/**
	 * Appends static contexts into the consumer. No subject required.
	 */
	public function calculateStatic(ContextConsumer $consumer) : void;
}
