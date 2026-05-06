<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\store;

/**
 * Stores active web editor sessions keyed by their bytebin paste ID.
 * Mirrors WebEditorSessionMap.java from the LuckPerms common module.
 */
class WebEditorSessionMap{
	/** @var array<string, RemoteSession> */
	private array $sessions = [];

	/**
	 * Register a new session created by an editor upload.
	 */
	public function addNewSession(string $pasteId, mixed $request) : void{
		$this->sessions[$pasteId] = new RemoteSession($pasteId, $request);
	}

	/**
	 * Retrieve a session by paste ID, or null if unknown.
	 */
	public function getSession(string $pasteId) : ?RemoteSession{
		return $this->sessions[$pasteId] ?? null;
	}

	public function remove(string $id) : void{
		unset($this->sessions[$id]);
	}

	/** @return array<string, RemoteSession> */
	public function all() : array{
		return $this->sessions;
	}

}
