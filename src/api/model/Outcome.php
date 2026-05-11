<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model;

/**
 * The various outcomes of a PlayerSaveResult operation.
 */
enum Outcome : string
{
	case CLEAN_INSERT = 'CLEAN_INSERT';
	case NO_CHANGE = 'NO_CHANGE';
	case USERNAME_UPDATED = 'USERNAME_UPDATED';
	case OTHER_UNIQUE_IDS_PRESENT_FOR_USERNAME = 'OTHER_UNIQUE_IDS_PRESENT_FOR_USERNAME';
}
