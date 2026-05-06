<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor;

use jasonw4331\LuckPerms\config\ConfigKeys;
use jasonw4331\LuckPerms\LuckPerms;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use function is_string;
use function rtrim;

/**
 * Encapsulates a session with the LuckPerms web editor.
 * Mirrors WebEditorSession.java from the LuckPerms common module.
 */
class WebEditorSession{

	private ?string $key = null;

	public function __construct(
		private LuckPerms $plugin,
		private CommandSender $sender,
		private string $cmdLabel
	){ }

	/**
	 * Build the editor payload, upload it to bytebin, and return the editor URL.
	 * Returns null on failure (error message already sent to sender).
	 */
	public function open() : ?string{
		$request = WebEditorRequest::generate($this->plugin, $this->sender, $this->cmdLabel);

		try{
			$content = $this->plugin->getBytebin()->postContent(
				$request->encode(),
				'application/json; charset=utf-8',
				'editor'
			);
		}catch(\Throwable $e){
			$this->sender->sendMessage(TextFormat::RED . 'Failed to upload to bytebin: ' . $e->getMessage());
			return null;
		}

		$this->key = $content->getKey();

		$base = 'https://luckperms.net/editor/';
		try{
			$configuredRaw = $this->plugin->getConfiguration()->get(ConfigKeys::WEB_EDITOR_URL_PATTERN());
			$configured = is_string($configuredRaw) ? $configuredRaw : '';
			if($configured !== ''){
				$base = $configured;
			}
		}catch(\Throwable){
			// keep default
		}

		return rtrim($base, '/') . '/' . $this->key;
	}

	public function getKey() : ?string{
		return $this->key;
	}
}
