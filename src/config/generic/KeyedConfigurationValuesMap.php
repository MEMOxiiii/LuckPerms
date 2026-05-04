<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\config\generic;

use jasonw4331\LuckPerms\config\generic\key\ConfigKey;

/**
 * @template Tvalue of object
 */
class KeyedConfigurationValuesMap{

	/** @var array<int, mixed> $values */
	private array $values;

	public function __construct(int $size){
		$this->values = array_fill(0, $size, null);
	}

	/**
	 * @phpstan-param ConfigKey<Tvalue> $key
	 * @phpstan-return Tvalue
	 */
	public function get(ConfigKey $key) : mixed{
		return $this->values[$key->ordinal()];
	}

	/**
	 * @phpstan-param ConfigKey<Tvalue> $key
	 */
	public function put(ConfigKey $key, mixed $value) : void{
		$this->values[$key->ordinal()] = $value;
	}
}
