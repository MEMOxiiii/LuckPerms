<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\store;

class WebEditorSessionMap{
	/** @var array<string, mixed> */
	private array $sessions = [];

	public function set(string $id, mixed $session) : void{
		$this->sessions[$id] = $session;
	}

	public function get(string $id) : mixed{
		return $this->sessions[$id] ?? null;
	}

	public function remove(string $id) : void{
		unset($this->sessions[$id]);
	}

	/** @return array<string, mixed> */
	public function all() : array{
		return $this->sessions;
	}

}
