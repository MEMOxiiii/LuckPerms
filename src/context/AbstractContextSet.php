<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context;

use jasonw4331\LuckPerms\api\context\ContextSatisfyMode;

/**
 * Abstract base providing shared context-set logic.
 */
abstract class AbstractContextSet extends ContextSet{
	/**
	 * Checks whether this context set is satisfied by another context set.
	 *
	 * @param array<string, array<string>> $other the contexts of the other set (key => [values])
	 * @param ContextSatisfyMode $mode
	 * @return bool
	 */
	public function isSatisfiedBy(array $other, ContextSatisfyMode $mode) : bool{
		foreach($this->toMultimap() as $key => $values){
			$otherValues = $other[$key] ?? [];
			if(empty($otherValues)){
				return false;
			}
			if($mode === ContextSatisfyMode::ALL_VALUES_PER_KEY()){
				foreach($values as $v){
					if(!in_array($v, $otherValues, true)){
						return false;
					}
				}
			} else { // AT_LEAST_ONE_VALUE_PER_KEY
				$found = false;
				foreach($values as $v){
					if(in_array($v, $otherValues, true)){
						$found = true;
						break;
					}
				}
				if(!$found){
					return false;
				}
			}
		}
		return true;
	}

	/** @return array<string, array<string>> key => [values] */
	abstract public function toMultimap() : array;
}

