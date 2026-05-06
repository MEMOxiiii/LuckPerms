<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\socket\listener;

/**
 * Abstract base for WebSocket message handlers.
 * Stub — WebSocket is not available in PocketMine-MP.
 */
abstract class Handler{
	/** @param array<string, mixed> $msg */
	abstract public function handle(array $msg) : void;
}
