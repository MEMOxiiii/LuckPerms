<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\locale;

use jasonw4331\LuckPerms\LuckPerms;

class TranslationRepository{
	public function __construct(private LuckPerms $plugin){ }

	public function scheduleRefresh() : void{
	}

}
