<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\query;

use jasonw4331\LuckPerms\util\traits\MixedRegistryTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static int ALL_FLAGS()
 * @method static Set ALL_FLAGS_SET()
 * @method static int ALL_FLAGS_SIZE()
 */
final class FlagUtils{
	use MixedRegistryTrait;

	private function __construct(){ }

	protected static function register(string $name, mixed $member) : void{
		self::_registryRegister($name, $member);
	}

	/**
	 * @return mixed[]
	 */
	public static function getAll() : array{
		//phpstan doesn't support generic traits yet :(
		/** @var mixed[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup() : void{
		self::register("ALL_FLAGS_SET", Flag::getAll());
		self::register("ALL_FLAGS_SIZE", count(self::ALL_FLAGS_SET()));
		self::register("ALL_FLAGS", self::toByte0(self::ALL_FLAGS_SET()));
	}

	public static function read(int $b, Flag $setting) : bool{
		return ($b >> $setting->ordinal() & 1) == 1; // TODO: ordinal PR for PocketMine
	}

	/**
	 * @param array<Flag> $settings
	 */
	public static function toByte(array $settings) : int{
		if(count($settings) === self::ALL_FLAGS_SIZE()){
			return self::ALL_FLAGS();
		}
		return self::toByte0($settings);
	}

	/**
	 * @param array<Flag> $settings
	 */
	private static function toByte0(array $settings) : int{
		$b = 0;
		foreach($settings as $setting){
			$b |= 1 << $setting->ordinal();
		}
		return $b;
	}

	public static function toSet(int $b) : array{
		$settings = [];
		foreach(Flag::getAll() as $setting){
			if(self::read($b, $setting)){
				$settings[] = $setting;
			}
		}
		return $settings;
	}
}
