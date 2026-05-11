<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\cacheddata\type;

use function array_key_exists;

/**
 * Stores resolved meta (prefix/suffix/meta-key) lookups for a single query-options context.
 */
class MetaCache{
	/** @var array<string, string|null> */
	private array $meta = [];
	private ?string $prefix = null;
	private ?string $suffix = null;
	private ?string $primaryGroup = null;

	public function getMeta(string $key) : string|null|false{
		if(!array_key_exists($key, $this->meta)){
			return false;
		}
		return $this->meta[$key];
	}

	public function setMeta(string $key, ?string $value) : void{
		$this->meta[$key] = $value;
	}

	/** @return array<string, string|null> */
	public function getAllMeta() : array{
		return $this->meta;
	}

	public function getPrefix() : ?string{
		return $this->prefix;
	}

	public function setPrefix(?string $prefix) : void{
		$this->prefix = $prefix;
	}

	public function getSuffix() : ?string{
		return $this->suffix;
	}

	public function setSuffix(?string $suffix) : void{
		$this->suffix = $suffix;
	}

	public function getPrimaryGroup() : ?string{
		return $this->primaryGroup;
	}

	public function setPrimaryGroup(?string $primaryGroup) : void{
		$this->primaryGroup = $primaryGroup;
	}

	public function invalidate() : void{
		$this->meta = [];
		$this->prefix = null;
		$this->suffix = null;
		$this->primaryGroup = null;
	}
}
