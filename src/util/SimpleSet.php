<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\util;

use Ramsey\Collection\AbstractSet;

/**
 * PHP 8.0-compatible replacement for Ramsey\Collection\Set
 */
class SimpleSet extends AbstractSet{

	public function __construct(private string $setType, array $data = []){
		parent::__construct($data);
	}

	public function getType() : string{
		return $this->setType;
	}
}
