<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\socket;

use function bin2hex;
use function random_bytes;

/**
 * Cryptography utilities for the web editor socket connection.
 * Stub — WebSocket/signature verification is not available in the PocketMine-MP port.
 */
class CryptographyUtils{

	/** Verify an Ed25519 signature (stub, always returns false). */
	public static function verify(string $publicKeyB64, string $data, string $signatureB64) : bool{
		return false;
	}

	/** Generate a random nonce for socket handshake (stub). */
	public static function generateNonce() : string{
		return bin2hex(random_bytes(16));
	}
}
