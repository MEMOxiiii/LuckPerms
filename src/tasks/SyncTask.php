<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\tasks;

use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;

class SyncTask{
	public function __construct(private LuckPerms $plugin){}

	public function run() : void{
		$this->plugin->getStorage()->loadAllGroups();
		$this->plugin->getStorage()->loadAllTracks();
		PermissionHelper::refreshAll($this->plugin);
	}
}
