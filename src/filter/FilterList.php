<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

use function array_map;
use function implode;
use function strtolower;

/**
 * A list of {@link Filter}s combined with a logical operator (AND or OR).
 *
 * @template T
 */
class FilterList{
	/** @var Filter[] */
	private array $filters;
	private LogicalOperator $operator;

	/** @param Filter[] $filters */
	public function __construct(LogicalOperator $operator, array $filters){
		$this->operator = $operator;
		$this->filters = $filters;
	}

	/**
	 * Create an empty filter list (matches everything).
	 */
	public static function empty() : self{
		return new self(LogicalOperator::AND, []);
	}

	/**
	 * Create an AND filter list.
	 */
	public static function and(Filter ...$filters) : self{
		return new self(LogicalOperator::AND, $filters);
	}

	/**
	 * Create an OR filter list.
	 */
	public static function or(Filter ...$filters) : self{
		return new self(LogicalOperator::OR, $filters);
	}

	public function operator() : LogicalOperator{
		return $this->operator;
	}

	/** @return Filter[] */
	public function getFilters() : array{
		return $this->filters;
	}

	/**
	 * Evaluate the filter list against a value.
	 * An empty list matches everything.
	 */
	public function evaluate(mixed $value) : bool{
		if(empty($this->filters)){
			return true;
		}
		return $this->operator->match($this->filters, $value);
	}

	public function __toString() : string{
		$op = strtolower($this->operator->name);
		return implode(' ' . $op . ' ', array_map('strval', $this->filters));
	}
}
