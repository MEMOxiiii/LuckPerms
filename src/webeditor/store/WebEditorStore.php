<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\store;

use jasonw4331\LuckPerms\LuckPerms;
use RuntimeException;

class WebEditorStore{

	private WebEditorSessionMap $sessions;
	private WebEditorSocketMap $sockets;
	private WebEditorKeystore $keystore;

	public function __construct(LuckPerms $plugin) {
		$this->sessions = new WebEditorSessionMap();
		$this->sockets = new WebEditorSocketMap();
		$this->keystore = new WebEditorKeystore($plugin->getDataFolder() . 'editor-keystore.json');
	}

	public function sessions() : WebEditorSessionMap{
		return $this->sessions;
	}

	public function sockets() : WebEditorSocketMap{
		return $this->sockets;
	}

	public function keystore() : WebEditorKeystore{
		return $this->keystore;
	}

	/**
	 * The original Java implementation uses async keypair generation.
	 * This port does not ship that layer yet, so callers should use the keystore directly.
	 */
	public function keyPair() : array{
		$keyPair = $this->keystore->get('default');
		if($keyPair === null){
			throw new RuntimeException('Web editor keypair is not available in keystore');
		}

		return $keyPair;
	}

}
