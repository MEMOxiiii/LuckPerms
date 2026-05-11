<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Represents the mode used when deciding if a set of contexts satisfies a requirement.
 */
enum ContextSatisfyMode : string
{
	/**
	 * All of the required contexts must be present.
	 */
	case ALL_VALUES_MUST_MATCH = 'ALL_VALUES_MUST_MATCH';

	/**
	 * At least one of the required contexts must be present.
	 */
	case AT_LEAST_ONE_VALUE_MUST_MATCH = 'AT_LEAST_ONE_VALUE_MUST_MATCH';
}
