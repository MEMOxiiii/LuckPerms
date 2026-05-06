<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\util;

use Ramsey\Collection\AbstractArray;
use Ramsey\Collection\QueueInterface;
use function current;
use function key;
use function reset;

/**
 * PHP 8.0-compatible replacement for Ramsey\Collection\Queue
 */
class SimpleQueue extends AbstractArray implements QueueInterface{

	public function __construct(private string $queueType, array $data = []){
		parent::__construct($data);
	}

	public function getType() : string{
		return $this->queueType;
	}

	public function add(mixed $element) : bool{
		return $this->offer($element);
	}

	public function offer(mixed $element) : bool{
		$this->offsetSet(null, $element);
		return true;
	}

	public function poll() : mixed{
		if($this->isEmpty()){
			return null;
		}
		$arr = $this->toArray();
		reset($arr);
		$key = key($arr);
		$value = current($arr);
		if($key !== null) $this->offsetUnset($key);
		return $value;
	}

	public function element() : mixed{
		if($this->isEmpty()){
			throw new \RuntimeException('Queue is empty');
		}
		$arr = $this->toArray();
		return reset($arr);
	}

	public function peek() : mixed{
		if($this->isEmpty()){
			return null;
		}
		$arr = $this->toArray();
		return reset($arr);
	}

	public function remove() : mixed{
		return $this->poll();
	}
}
