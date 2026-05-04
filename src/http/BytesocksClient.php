<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\http;

use pocketmine\utils\InternetException;
use function ltrim;
use function parse_url;
use function rtrim;
use const PHP_URL_HOST;

class BytesocksClient extends AbstractHttpClient{

	private string $httpUrl;
	private string $wsUrl;

	public function __construct(string $host, private string $userAgent){
		$normalizedHost = (string) (parse_url($host, PHP_URL_HOST) ?? $host);
		$normalizedHost = ltrim(rtrim($normalizedHost, '/'), '/');

		$this->httpUrl = 'https://' . $normalizedHost . '/';
		$this->wsUrl = 'wss://' . $normalizedHost . '/';
	}

	public function createSocket(\Closure $webSocketListener) : \Socket {
		$sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if($sock === false){
			throw new InternetException("Failed to get internal IP: " . trim(socket_strerror(socket_last_error())));
		}

		// Full bytesocks/websocket negotiation is not implemented in this project yet.
		return $sock;
	}

}
