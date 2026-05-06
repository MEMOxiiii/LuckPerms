<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\store;

/**
 * Represents a remote web editor session tied to a specific bytebin paste.
 * Mirrors RemoteSession.java from the LuckPerms common module.
 */
class RemoteSession{

	private bool $completed = false;

	public function __construct(
		private string $pasteId,
		private mixed $request
	){ }

	public function getPasteId() : string{
		return $this->pasteId;
	}

	public function request() : mixed{
		return $this->request;
	}

	public function isCompleted() : bool{
		return $this->completed;
	}

	public function complete() : void{
		$this->completed = true;
	}
}
