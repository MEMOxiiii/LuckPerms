<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\metastacking;

use jasonw4331\LuckPerms\api\metastacking\DuplicateRemovalFunction;
use jasonw4331\LuckPerms\api\metastacking\MetaStackDefinition;

class SimpleMetaStackDefinition implements MetaStackDefinition{

	private array $elements = [];
	private DuplicateRemovalFunction $duplicateRemovalFunction;
	private string $startSpacer;
	private string $middleSpacer;
	private string $endSpacer;

	// cache hashcode - this class is immutable, and used an index in MetaContexts
	private int $hashCode;
	private $parseList;

	public function __construct(array $elements, DuplicateRemovalFunction $duplicateRemovalFunction, string $startSpacer, string $middleSpacer, string $endSpacer){
		$this->elements = $elements;
		$this->duplicateRemovalFunction = $duplicateRemovalFunction;
		$this->startSpacer = $startSpacer;
		$this->middleSpacer = $middleSpacer;
		$this->endSpacer = $endSpacer;
		$this->hashCode = $this->calculateHashCode();
	}

	public function getElements() : array{
		return $this->elements;
	}

	public function getDuplicateRemovalFunction() : DuplicateRemovalFunction{
		return $this->duplicateRemovalFunction;
	}

	public function getStartSpacer() : string{
		return $this->startSpacer;
	}

	public function getMiddleSpacer() : string{
		return $this->middleSpacer;
	}

	public function getEndSpacer() : string{
		return $this->endSpacer;
	}

	private function calculateHashCode() : int{
		return 0; // TODO: hash multiple object together
	}
}
