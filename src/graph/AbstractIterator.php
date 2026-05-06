<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\graph;

abstract class AbstractIterator implements \Iterator{
	private const STATE_READY = 1;
	private const STATE_NOT_READY = 2;
	private const STATE_DONE = 3;
	private const STATE_FAILED = 4;

	private int $state = self::STATE_NOT_READY;
	private mixed $nextValue = null;
	private int $position = 0;

	abstract protected function computerNext();

	protected function endOfData(){
		$this->state = self::STATE_DONE;
		return null;
	}

	private function tryToComputeNext() : bool{
		$this->state = self::STATE_FAILED;
		$this->nextValue = $this->computerNext();
		if($this->state !== self::STATE_DONE){
			$this->state = self::STATE_READY;
			return true;
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function current(){
		if(!$this->valid()){
			return null;
		}
		return $this->nextValue;
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function next(){
		if($this->valid()){
			$this->position++;
		}
		$this->state = self::STATE_NOT_READY;
		return $this->current();
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function key(){
		return $this->position;
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function valid(){
		if($this->state === self::STATE_FAILED){
			throw new \RuntimeException('Iterator is in failed state');
		}
		if($this->state === self::STATE_DONE){
			return false;
		}
		if($this->state === self::STATE_READY){
			return true;
		}
		return $this->tryToComputeNext();
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function rewind(){
		if($this->position !== 0 || $this->state !== self::STATE_NOT_READY){
			throw new \LogicException('This iterator does not support rewind');
		}
	}
}
