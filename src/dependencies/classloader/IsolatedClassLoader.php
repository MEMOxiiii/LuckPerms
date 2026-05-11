<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\dependencies\classloader;

/**
 * No-op class loader for PocketMine — Composer handles all dependencies.
 */
class IsolatedClassLoader{
public function addJarToClasspath(string $jarPath) : void{ }
public function loadClass(string $className) : void{ }
}
