<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context;

use jasonw4331\LuckPerms\api\context\Context;
use jasonw4331\LuckPerms\api\context\ContextSatisfyMode;
use function array_filter;
use function array_values;
use function count;

class MutableContextSetImpl extends MutableContextSet{

	/** @var array<int, Context> */
	private array $contexts = [];

	public function isImmutable() : bool{
		return false;
	}

	public function immutableCopy() : ImmutableContextSet{
		return new ImmutableContextSetImpl($this->contexts);
	}

	public function mutableCopy() : MutableContextSet{
		$copy = new self();
		$copy->contexts = $this->contexts;
		return $copy;
	}

	public function toSet() : array{
		return $this->contexts;
	}

	public function toMap() : array{
		$map = [];
		foreach($this->contexts as $ctx){
			$map[$ctx->getKey()][] = $ctx->getValue();
		}
		return $map;
	}

	public function toFlattenedMap() : array{
		$map = [];
		foreach($this->contexts as $ctx){
			$map[$ctx->getKey()] = $ctx->getValue();
		}
		return $map;
	}

	public function getIterator() : \ArrayIterator{
		return new \ArrayIterator($this->contexts);
	}

	public function containsKey(string $key) : bool{
		foreach($this->contexts as $ctx){
			if($ctx->getKey() === $key) return true;
		}
		return false;
	}

	public function getValues(string $key) : array{
		$values = [];
		foreach($this->contexts as $ctx){
			if($ctx->getKey() === $key) $values[] = $ctx->getValue();
		}
		return $values;
	}

	public function contains(string $key, string $value) : bool{
		foreach($this->contexts as $ctx){
			if($ctx->getKey() === $key && $ctx->getValue() === $value) return true;
		}
		return false;
	}

	public function isSatisfiedByMode(ContextSet $other, ContextSatisfyMode $mode) : bool{
		foreach($this->contexts as $ctx){
			if(!$other->contains($ctx->getKey(), $ctx->getValue())) return false;
		}
		return true;
	}

	public function isEmpty() : bool{
		return $this->contexts === [];
	}

	public function size() : int{
		return count($this->contexts);
	}

	public function add(string $key, string $value) : void{
		$this->contexts[] = new ContextImpl($key, $value);
	}

	public function addAllContexts(ContextSet $contextSet) : void{
		foreach($contextSet as $ctx){
			if($ctx instanceof Context) $this->contexts[] = $ctx;
		}
	}

	public function remove(string $key, string $value) : void{
		$this->contexts = array_values(array_filter($this->contexts, fn(Context $ctx) => !($ctx->getKey() === $key && $ctx->getValue() === $value)));
	}

	public function removeAll(string $key) : void{
		$this->contexts = array_values(array_filter($this->contexts, fn(Context $ctx) => $ctx->getKey() !== $key));
	}

	public function clear() : void{
		$this->contexts = [];
	}
}
