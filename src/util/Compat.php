<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\util;

use Ramsey\Collection\AbstractArray;
use Ramsey\Collection\AbstractSet;
use Ramsey\Collection\Map\AbstractTypedMap;
use Ramsey\Collection\QueueInterface;
use function array_pop;
use function array_unshift;
use function current;
use function end;
use function key;
use function reset;

/**
 * PHP 8.0-compatible replacement for Ramsey\Collection\Map\TypedMap
 * (the original requires PHP 8.1 readonly properties)
 */
class SimpleTypedMap extends AbstractTypedMap{

	public function __construct(
		private string $keyType,
		private string $valueType,
		array $data = []
	){
		parent::__construct($data);
	}

	public function getKeyType() : string{
		return $this->keyType;
	}

	public function getValueType() : string{
		return $this->valueType;
	}
}

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
		$this->offsetUnset($key);
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

/**
 * PHP 8.0-compatible replacement for Ramsey\Collection\DoubleEndedQueue
 */
class SimpleDoubleEndedQueue extends AbstractArray implements \Ramsey\Collection\DoubleEndedQueueInterface{

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

	public function addFirst(mixed $element) : bool{
		$arr = $this->getArrayCopy();
		array_unshift($arr, $element);
		$this->exchangeArray($arr);
		return true;
	}

	public function addLast(mixed $element) : bool{
		$this->offer($element);
		return true;
	}

	public function offerFirst(mixed $element) : bool{
		$this->addFirst($element);
		return true;
	}

	public function offerLast(mixed $element) : bool{
		return $this->offer($element);
	}

	public function removeFirst() : mixed{
		if($this->isEmpty()) throw new \RuntimeException('Deque is empty');
		$arr = $this->toArray();
		reset($arr);
		$key = key($arr);
		$value = current($arr);
		$this->offsetUnset($key);
		return $value;
	}

	public function removeLast() : mixed{
		if($this->isEmpty()) throw new \RuntimeException('Deque is empty');
		$arr = $this->getArrayCopy();
		$value = array_pop($arr);
		$this->exchangeArray($arr);
		return $value;
	}

	public function pollFirst() : mixed{
		if($this->isEmpty()) return null;
		return $this->removeFirst();
	}

	public function pollLast() : mixed{
		if($this->isEmpty()) return null;
		return $this->removeLast();
	}

	public function firstElement() : mixed{
		if($this->isEmpty()) throw new \RuntimeException('Deque is empty');
		$arr = $this->toArray();
		return reset($arr);
	}

	public function lastElement() : mixed{
		if($this->isEmpty()) throw new \RuntimeException('Deque is empty');
		$arr = $this->toArray();
		return end($arr);
	}

	public function peekFirst() : mixed{
		if($this->isEmpty()) return null;
		return $this->firstElement();
	}

	public function peekLast() : mixed{
		if($this->isEmpty()) return null;
		return $this->lastElement();
	}

	public function poll() : mixed{
		return $this->pollFirst();
	}

	public function remove() : mixed{
		return $this->removeFirst();
	}

	public function element() : mixed{
		return $this->firstElement();
	}

	public function peek() : mixed{
		return $this->peekFirst();
	}

	private function getArrayCopy() : array{
		return $this->toArray();
	}

	private function exchangeArray(array $arr) : void{
		foreach($this->toArray() as $k => $_){
			$this->offsetUnset($k);
		}
		foreach($arr as $v){
			$this->offsetSet(null, $v);
		}
	}
}
