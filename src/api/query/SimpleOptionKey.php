<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query;

use function strtolower;

final class SimpleOptionKey implements OptionKey
{
	private string $name;
	private string $type;

	public function __construct(string $name, string $type)
	{
		$this->name = strtolower($name);
		$this->type = $type;
	}

	public static function of(string $name, string $type) : OptionKey
	{
		return new self($name, $type);
	}

	public function name() : string
	{
		return $this->name;
	}

	public function type() : string
	{
		return $this->type;
	}

	public function __toString() : string
	{
		return 'OptionKey(name=' . $this->name . ', type=' . $this->type . ')';
	}

	public function equals(mixed $other) : bool
	{
		if (!$other instanceof SimpleOptionKey) {
			return false;
		}
		return $this->name === $other->name && $this->type === $other->type;
	}
}
