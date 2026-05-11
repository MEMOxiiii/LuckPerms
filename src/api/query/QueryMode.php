<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query;

enum QueryMode : string
{
	/**
	 * Contextual query - uses contexts to filter data.
	 */
	case CONTEXTUAL = 'CONTEXTUAL';

	/**
	 * Non-contextual query - does not use contexts to filter data.
	 */
	case NON_CONTEXTUAL = 'NON_CONTEXTUAL';
}
