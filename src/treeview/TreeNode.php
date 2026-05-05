<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\treeview;


class TreeNode{

	private static function allowInsert(TreeNode $node) : bool{
		// level 0    =>  no limit
		// level 1/2  =>  up to 500
		// level 3+   =>  up to 100

		if($node->level === 0){
			return true;
		}elseif($node->level <= 2){
			return $node->getChildrenSize() < 500;
		}else{
			return $node->getChildrenSize() < 100;
		}
	}

	/** @var array<string, TreeNode>|null $children */
	private ?array $children = null;
	private int $level;

	public function __construct(?TreeNode $parent = null){
		$this->level = $parent?->level + 1 ?? 0;
	}

	// lazy init
	private function getChildMap() : array{
		if($this->children === null){
			$this->children = [];
		}
		return $this->children;
	}

	public function tryInsert(string $s) : ?TreeNode{
		if(!$this->allowInsert($this)){
			return null;
		}
		if($this->children === null) $this->children = [];
		if(!isset($this->children[$s])){
			$this->children[$s] = new TreeNode($this);
		}
		return $this->children[$s];
	}

	public function getChildren() : ?array{
		return $this->children;
	}

	public function getChildrenSize() : int{
				return $this->children !== null ? count($this->children) : 0;
	}

	public function makeImmutableCopy() : ImmutableTreeNode{
		$array = [];
		foreach($this->children ?? [] as $key => $node){
			$array[$key] = $node->makeImmutableCopy();
		}
		return new ImmutableTreeNode($array);
	}
}
