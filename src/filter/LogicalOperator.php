<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

/**
 * Logical operators for combining filters in a {@link FilterList}.
 */
enum LogicalOperator{
	case AND;
	case OR;

	/**
	 * Evaluate whether the given filters match the value using this operator.
	 *
	 * @param Filter[] $filters
	 */
	public function match(array $filters, mixed $value) : bool{
		return match ($this) {
			self::AND => $this->matchAll($filters, $value),
			self::OR => $this->matchAny($filters, $value),
		};
	}

	private function matchAll(array $filters, mixed $value) : bool{
		foreach($filters as $filter){
			if(!$filter->evaluate($value)){
				return false;
			}
		}
		return true;
	}

	private function matchAny(array $filters, mixed $value) : bool{
		foreach($filters as $filter){
			if($filter->evaluate($value)){
				return true;
			}
		}
		return false;
	}
}
