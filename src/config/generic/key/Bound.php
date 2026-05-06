<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\config\generic\key;

use jasonw4331\LuckPerms\config\generic\adapter\ConfigurationAdapter;

/**
 * @template Tvalue
 */
class Bound{

	/**
	 * @param ConfigKeyFactory<Tvalue> $factory
	 * @param Tvalue                   $def
	 */
	public function __construct(private ConfigKeyFactory $factory, private string $path, private mixed $def){ }

	/**
	 * @return Tvalue
	 */
	public function __invoke(ConfigurationAdapter $adapter) : mixed{
		$method = $this->factory->getFunctionName();
		// @phpstan-ignore-next-line
		return $adapter->$method($this->path, $this->def);
	}
}
