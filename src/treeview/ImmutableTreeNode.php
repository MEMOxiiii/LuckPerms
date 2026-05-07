<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\treeview;

use function uksort;

class ImmutableTreeNode{
	/** @var array<string, ImmutableTreeNode>|null $children */
	private ?array $children;

	public function __construct(?array $children){
		if($children !== null){
			uksort($children, 'strcasecmp');
			$this->children = $children;
		}else{
			$this->children = null;
		}
	}

	public function getChildren() : ?array{
		return $this->children !== null ? $this->children : null;
	}

	public function getNodeEndings() : array{
		if($this->children === null){
			return [];
		}

		$results = [];
		foreach($this->children as $value => $node){
			// add self
			$results[] = $value;

			// add child nodes with their full dotted path
			foreach($node->getNodeEndings() as $childNode){
				$results[] = $value . "." . $childNode;
			}
		}

		return $results;
	}

	public function toJson(string $prefix) : \stdClass{
		if($this->children === null){
			return (object) [];
		}

		$object = new \stdClass();
		foreach($this->children as $key => $value){
			$object->{$key} = $value->toJson($prefix . $key . ".");
		}
		return $object;
	}

	public function compareTo(ImmutableTreeNode $o) : int{
		return ($this->children !== null) === ($o->getChildren() !== null) ? 0 : ($this->children !== null ? 1 : -1);
	}
}
