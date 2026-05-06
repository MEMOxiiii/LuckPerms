<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context\calculator;

use jasonw4331\LuckPerms\api\context\ContextConsumer;
use function count;

abstract class WorldNameRewriter{

	/**
	 * @param TypedMap<string, string> $rewrites
	 */
	public static function of(array $rewrites) : WorldNameRewriter{
		if(count($rewrites) < 1){
			return new EmptyWorldNameRewriter();
		}
		return new NonEmptyWorldNameRewriter($rewrites);
	}

	abstract public function rewriteAndSubmit(string $worldName, ContextConsumer $consumer) : void;
}
