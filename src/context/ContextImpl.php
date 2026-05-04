<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context;

use jasonw4331\LuckPerms\api\context\Context;

final class ContextImpl extends Context{

	public function __construct(private string $key, private string $value){ }

	public function getKey() : string{
		return $this->key;
	}

	public function getValue() : string{
		return $this->value;
	}
}
