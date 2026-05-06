<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\http;

final class Content{

	public function __construct(private string $key) {}

	public function getKey() : string{
		return $this->key;
	}

}
