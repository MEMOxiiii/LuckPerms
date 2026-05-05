<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\model;

class Track{
	public function __construct(private string $name){ }

	public function getName() : string{
		return $this->name;
	}

}
