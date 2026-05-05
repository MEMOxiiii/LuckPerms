<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\config\generic\adapter;

use jasonw4331\LuckPerms\LuckPerms;

use function mb_strtolower;

class SystemPropertyConfigAdapter implements ConfigurationAdapter{

	public function __construct(private LuckPerms $luckPerms){}

	public function getPlugin() : LuckPerms{
		return $this->luckPerms;
	}

	public function reload() : void{}

	public function getString(string $path, ?string $def) : string{
		return $def ?? '';
	}

	public function getLowercaseString(string $path, ?string $def) : string{
		return mb_strtolower($this->getString($path, $def));
	}

	public function getInteger(string $path, int $def) : int{
		return $def;
	}

	public function getBoolean(string $path, bool $def) : bool{
		return $def;
	}

	public function getStringList(string $path, array $def) : array{
		return $def;
	}

	public function getStringMap(string $path, array $def) : array{
		return $def;
	}

}
