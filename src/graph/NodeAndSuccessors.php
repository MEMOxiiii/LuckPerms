<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\graph;

final class NodeAndSuccessors{
	public $node;
	public object $successorIterator;

	public function __construct($node, \Traversable $successors){
		$this->node = $node;
		$this->successorIterator = new class($successors){
			/** @var list<mixed> */
			private array $items = [];
			private int $index = 0;

			public function __construct(\Traversable $successors){
				foreach($successors as $item){
					$this->items[] = $item;
				}
			}

			public function hasNext() : bool{
				return $this->index < count($this->items);
			}

			public function next(){
				if(!$this->hasNext()){
					return null;
				}
				return $this->items[$this->index++];
			}
		};
	}
}
