<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\dependencies\relocation;

/**
 * No-op relocation handler — PocketMine does not use JAR relocation.
 */
class RelocationHandler{
public function process(string $input, string $output, array $relocations) : void{ }
}
