<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context;

use jasonw4331\LuckPerms\api\context\Context;
use jasonw4331\LuckPerms\api\context\ContextSatisfyMode;

final class ImmutableContextSetImpl extends ImmutableContextSet{

	public static function EMPTY() : ImmutableContextSet{
		return new self([]);
	}

	/** @var Context[] $array */
	private array $array;
	private int $size;

	/** @var array<string, array<string>> $cachedMap */
	private array $cachedMap = [];

	public function __construct(array $contexts = []){
		$this->array = $contexts;
		$this->size = \count($this->array);
	}

	public function isImmutable() : bool{
		return true;
	}

	public function immutableCopy() : ImmutableContextSetImpl{
		return $this;
	}

	/**
	 * @return array<string, array<string>>
	 */
	public function toMultimap() : array{
		if(empty($this->cachedMap)){
			foreach($this->array as $entry){
				$this->cachedMap[$entry->getKey()][] = $entry->getValue();
			}
		}
		return $this->cachedMap;
	}

	public function mutableCopy() : MutableContextSet{
		$copy = new MutableContextSetImpl();
		foreach($this->array as $ctx){
			$copy->addContext($ctx);
		}
		return $copy;
	}

	/**
	 * @return Context[]
	 */
	public function toSet() : array{
		return $this->array;
	}

	/**
	 * @return array<string, array<string>>
	 */
	public function toMap() : array{
		return $this->toMultimap();
	}

	/**
	 * @return array<string, string>
	 */
	public function toFlattenedMap() : array{
		$m = [];
		foreach($this->array as $e){
			$m[$e->getKey()] = $e->getValue();
		}
		return $m;
	}

	/**
	 * @return Context[]
	 */
	public function toArray() : array{
		return $this->array; // only used read-only & internally
	}

	public function getIterator() : \ArrayIterator{
		return new \ArrayIterator($this->array);
	}

	public function containsKey(string $key) : bool{
		foreach($this->array as $ctx){
			if($ctx->getKey() === $key) return true;
		}
		return false;
	}

	public function getValues(string $key) : array{
		$values = [];
		foreach($this->array as $ctx){
			if($ctx->getKey() === $key) $values[] = $ctx->getValue();
		}
		return $values;
	}

	public function contains(string $key, string $value) : bool{
		foreach($this->array as $ctx){
			if($ctx->getKey() === $key && $ctx->getValue() === $value) return true;
		}
		return false;
	}

	public function isSatisfiedByMode(ContextSet $other, ContextSatisfyMode $mode) : bool{
		if($this->isEmpty()) return true;
		foreach($this->array as $ctx){
			if(!$other->contains($ctx->getKey(), $ctx->getValue())) return false;
		}
		return true;
	}

	public function isEmpty() : bool{
		return $this->size === 0;
	}

	public function size() : int{
		return $this->size;
	}
}
