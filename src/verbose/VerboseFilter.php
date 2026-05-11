<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\verbose;

use jasonw4331\LuckPerms\verbose\event\VerboseEvent;
use function preg_match;
use function strtolower;

/**
 * Represents a verbose filter expression.
 *
 * Supports:
 *   - empty string / "any" — matches all events
 *   - plain string — matches events where target OR permission contains the string
 *   - "player:name" — matches a specific player target
 *   - "permission:node" — matches a specific permission node
 *   - "result:true|false|undefined" — matches by result value
 */
final class VerboseFilter{
	private string $expression;
	/** @var callable(VerboseEvent): bool */
	private $evaluator;

	private function __construct(string $expression, callable $evaluator){
		$this->expression = $expression;
		$this->evaluator = $evaluator;
	}

	/**
	 * Creates a filter that accepts all events.
	 */
	public static function acceptAll() : self{
		return new self('', static fn() => true);
	}

	/**
	 * Compile a filter from a string expression.
	 * Throws {@link InvalidFilterException} on parse error.
	 *
	 * @throws InvalidFilterException
	 */
	public static function compile(string $expression) : self{
		if($expression === '' || $expression === 'any'){
			return self::acceptAll();
		}

		// Supported prefixes
		if(str_starts_with($expression, 'player:')){
			$target = strtolower(substr($expression, 7));
			return new self($expression, static fn(VerboseEvent $e) => strtolower($e->getCheckTarget()->describe()) === $target);
		}
		if(str_starts_with($expression, 'permission:')){
			$perm = strtolower(substr($expression, 11));
			return new self($expression, static function(VerboseEvent $e) use ($perm) : bool{
				if(!method_exists($e, 'getPermission')){
					return false;
				}
				return strtolower($e->getPermission()) === $perm;
			});
		}
		if(str_starts_with($expression, 'result:')){
			$resultFilter = strtolower(substr($expression, 7));
			return new self($expression, static function(VerboseEvent $e) use ($resultFilter) : bool{
				if(!method_exists($e, 'getResult')){
					return false;
				}
				$result = $e->getResult();
				$resultName = method_exists($result, 'name') ? strtolower($result->name()) : strtolower((string) $result);
				return $resultName === $resultFilter;
			});
		}

		// Default: substring match on target or permission
		$lower = strtolower($expression);
		return new self($expression, static function(VerboseEvent $e) use ($lower) : bool{
			if(str_contains(strtolower($e->getCheckTarget()->describe()), $lower)){
				return true;
			}
			if(method_exists($e, 'getPermission') && str_contains(strtolower($e->getPermission()), $lower)){
				return true;
			}
			if(method_exists($e, 'getKey') && str_contains(strtolower($e->getKey()), $lower)){
				return true;
			}
			return false;
		});
	}

	/**
	 * Evaluates whether the event passes this filter.
	 */
	public function evaluate(VerboseEvent $event) : bool{
		try{
			return ($this->evaluator)($event);
		}catch(\Throwable){
			return false;
		}
	}

	public function isBlank() : bool{
		return $this->expression === '';
	}

	public function __toString() : string{
		return $this->isBlank() ? 'any' : $this->expression;
	}
}

