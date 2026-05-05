<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api;

class ApiRegistrationUtil{
	public static function registerProvider(LuckPermsApiProvider $provider) : void{
		LuckPermsProvider::register($provider);
	}

	public static function unregisterProvider() : void{
		LuckPermsProvider::unregister();
	}

}
