<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\store;

class WebEditorSocketMap{
	/** @var array<string, mixed> */
	private array $sockets = [];

	public function set(string $id, mixed $socket) : void{
		$this->sockets[$id] = $socket;
	}

	public function get(string $id) : mixed{
		return $this->sockets[$id] ?? null;
	}

	public function remove(string $id) : void{
		unset($this->sockets[$id]);
	}

	/** @return array<string, mixed> */
	public function all() : array{
		return $this->sockets;
	}

}
