<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\config;

use jasonw4331\LuckPerms\config\generic\adapter\ConfigurationAdapter;
use jasonw4331\LuckPerms\config\generic\key\ConfigKey;

class LuckPermsConfiguration{

	public function __construct(private mixed $plugin, private ConfigurationAdapter $adapter){}

	/**
	 * @template T
	 * @phpstan-param ConfigKey<T> $key
	 * @phpstan-return T
	 */
	public function get(ConfigKey $key) : mixed{
		return $key->get($this->adapter);
	}

	public function reload() : void{
		if(method_exists($this->adapter, 'reload')){
			$this->adapter->reload();
		}
	}

	public function getPlugin() : mixed{
		return $this->plugin;
	}

}
