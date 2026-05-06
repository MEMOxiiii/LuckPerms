<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\query;

use jasonw4331\LuckPerms\api\query\QueryOptions;
use jasonw4331\LuckPerms\context\ImmutableContextSet;
use jasonw4331\LuckPerms\context\ImmutableContextSetImpl;
use function array_filter;
use function array_values;
use function count;
use function in_array;

class QueryOptionsBuilderImpl implements Builder{
	private QueryMode $mode;
	private ?ImmutableContextSet $context;
	private int $flags;
	/** @var array<Flag>|null */
	private ?array $flagsSet;
	private ?array $options;
	private bool $copyOptions;

	public function __construct(QueryMode $mode){
		$this->mode = $mode;
				$this->context = $mode === QueryMode::CONTEXTUAL() ? ImmutableContextSetImpl::empty() : null;
		$this->flags = FlagUtils::ALL_FLAGS();
		$this->flagsSet = null;
		$this->options = null;
		$this->copyOptions = false;
	}

	public function mode(QueryMode $mode) : Builder{
		if($this->mode === $mode){
			return $this;
		}

		$this->mode = $mode;
				$this->context = $this->mode === QueryMode::CONTEXTUAL() ? ImmutableContextSetImpl::empty() : null;
		return $this;
	}

	public function flag(Flag $flag, bool $value) : Builder{
		if($this->flagsSet === null && FlagUtils::read($this->flags, $flag) === $value){
			return $this;
		}

		if($this->flagsSet === null){
			$this->flagsSet = FlagUtils::toSet($this->flags);
		}
		if($value){
			// add if not already present
			if(!in_array($flag, $this->flagsSet, true)){
				$this->flagsSet[] = $flag;
			}
		}else{
			$this->flagsSet = array_values(array_filter($this->flagsSet, fn($f) => $f !== $flag));
		}

		return $this;
	}

	/**
	 * @param array<Flag> $flags
	 */
	public function flags(array $flags) : Builder{
		$this->flagsSet = $flags;
		return $this;
	}

	public function option($key, $value) : Builder{
		if($this->options === null || $this->copyOptions){
			if($this->options === null){
				$this->options = [];
			}
			$this->copyOptions = false;
		}
		if($value === null){
			unset($this->options[$key]);
		}else{
			$this->options[$key] = $value;
		}

		if(count($this->options) < 1){
			$this->options = null;
		}

		return $this;
	}

	public function build() : QueryOptions{
		$flags = $this->flagsSet !== null ? FlagUtils::toByte($this->flagsSet) : $this->flags;

		return new QueryOptionsImpl();
	}
}
