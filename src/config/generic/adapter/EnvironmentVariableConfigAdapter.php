<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\config\generic\adapter;

use jasonw4331\LuckPerms\LuckPerms;

use function getenv;
use function is_numeric;
use function is_string;
use function mb_strtolower;
use function preg_replace;
use function strtoupper;

class EnvironmentVariableConfigAdapter implements ConfigurationAdapter{

	public function __construct(private LuckPerms $luckPerms){}

	public function getPlugin() : LuckPerms{
		return $this->luckPerms;
	}

	public function reload() : void{}

	private function envName(string $path) : string{
		$normalized = preg_replace('/[^A-Za-z0-9]+/', '_', $path) ?? $path;
		return 'LUCKPERMS_' . strtoupper($normalized);
	}

	public function getString(string $path, ?string $def) : string{
		$value = getenv($this->envName($path));
		return is_string($value) && $value !== '' ? $value : ($def ?? '');
	}

	public function getLowercaseString(string $path, ?string $def) : string{
		return mb_strtolower($this->getString($path, $def));
	}

	public function getInteger(string $path, int $def) : int{
		$value = getenv($this->envName($path));
		return is_string($value) && is_numeric($value) ? (int) $value : $def;
	}

	public function getBoolean(string $path, bool $def) : bool{
		$value = getenv($this->envName($path));
		if(!is_string($value)){
			return $def;
		}
		$value = mb_strtolower($value);
		if($value === '1' || $value === 'true' || $value === 'yes' || $value === 'on'){
			return true;
		}
		if($value === '0' || $value === 'false' || $value === 'no' || $value === 'off'){
			return false;
		}
		return $def;
	}

	public function getStringList(string $path, array $def) : array{
		return $def;
	}

	public function getStringMap(string $path, array $def) : array{
		return $def;
	}

}
