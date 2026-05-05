<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api;

class LuckPermsProvider{
	private static ?LuckPermsApiProvider $provider = null;

	public static function register(LuckPermsApiProvider $provider) : void{
		self::$provider = $provider;
	}

	public static function unregister() : void{
		self::$provider = null;
	}

	public static function get() : LuckPermsApiProvider{
		if(self::$provider === null){
			throw new \RuntimeException('LuckPerms provider is not registered');
		}
		return self::$provider;
	}

}
