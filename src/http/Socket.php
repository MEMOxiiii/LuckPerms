<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\http;

final class Socket{

	public function __construct(private string $channelId, private \Socket $socket) {}

	public function getChannelId() : string{
		return $this->channelId;
	}

	public function getSocket() : \Socket{
		return $this->socket;
	}

}
