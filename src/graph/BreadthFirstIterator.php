<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\graph;

use jasonw4331\LuckPerms\util\SimpleDoubleEndedQueue;

final class BreadthFirstIterator implements \Iterator{
	private Graph $graph;

	private SimpleDoubleEndedQueue $queue;
	private array $visited = [];

	public function __construct(Graph $graph, $root){
		$this->graph = $graph;
				$this->queue = new SimpleDoubleEndedQueue($root::class, [$root]);
		$this->visited[] = $root;
	}

	public function valid() : bool{
		return !$this->queue->isEmpty();
	}

	public function next() : Graph{
		$current = $this->queue->remove();
		foreach($this->graph->successors($current) as $neighbor){
			$this->visited[] = $neighbor;
			$this->queue->add($neighbor);
		}
		return $current;
	}

	public function current() : ?Graph{
		return $this->queue->peek();
	}

	public function key(){
		throw new \Exception();
	}

	public function rewind(){
		throw new \Exception();
	}
}
