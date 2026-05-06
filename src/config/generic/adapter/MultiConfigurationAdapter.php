<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\config\generic\adapter;

use jasonw4331\LuckPerms\LuckPerms;

class MultiConfigurationAdapter implements ConfigurationAdapter{

	/** @var ConfigurationAdapter[] */
	private array $adapters;

	public function __construct(ConfigurationAdapter ...$adapters){
		$this->adapters = $adapters;
	}

	public function getPlugin() : LuckPerms{
		return $this->adapters[0]->getPlugin();
	}

	public function reload() : void{
		foreach($this->adapters as $adapter){
			$adapter->reload();
		}
	}

	public function getString(string $path, ?string $def) : string{
		$value = $def;
		foreach($this->adapters as $adapter){
			$value = $adapter->getString($path, $value);
		}
		return $value ?? '';
	}

	public function getLowercaseString(string $path, ?string $def) : string{
		$value = $def;
		foreach($this->adapters as $adapter){
			$value = $adapter->getLowercaseString($path, $value);
		}
		return $value ?? '';
	}

	public function getInteger(string $path, int $def) : int{
		$value = $def;
		foreach($this->adapters as $adapter){
			$value = $adapter->getInteger($path, $value);
		}
		return $value;
	}

	public function getBoolean(string $path, bool $def) : bool{
		$value = $def;
		foreach($this->adapters as $adapter){
			$value = $adapter->getBoolean($path, $value);
		}
		return $value;
	}

	public function getStringList(string $path, array $def) : array{
		$value = $def;
		foreach($this->adapters as $adapter){
			$value = $adapter->getStringList($path, $value);
		}
		return $value;
	}

	public function getStringMap(string $path, array $def) : array{
		$value = $def;
		foreach($this->adapters as $adapter){
			$value = $adapter->getStringMap($path, $value);
		}
		return $value;
	}

}
