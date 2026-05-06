<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\config\generic\adapter;

use jasonw4331\LuckPerms\LuckPerms;

interface ConfigurationAdapter{
	public function getPlugin() : LuckPerms;

	public function reload() : void;

	public function getString(string $path, ?string $def) : string;

	public function getLowercaseString(string $path, ?string $def) : string;

	public function getInteger(string $path, int $def) : int;

	public function getBoolean(string $path, bool $def) : bool;

	/**
	 * @param List<string> $def
	 *
	 * @return List<string>
	 */
	public function getStringList(string $path, array $def) : array;

	/**
	 * @param array<string, string> $def
	 *
	 * @return array<string, string>
	 */
	public function getStringMap(string $path, array $def) : array;
}
