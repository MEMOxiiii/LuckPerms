<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\util;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\User;
use Ramsey\Uuid\UuidInterface;

class AbstractConnectionListener{
	public function __construct(protected LuckPerms $plugin){ }

	public function loadUser(UuidInterface $uniqueId, string $username) : ?User{
		$user = $this->plugin->getUserManager()->load($uniqueId, $username);
		$this->plugin->getStorage()->saveUser($user);
		return $user;
	}

}
