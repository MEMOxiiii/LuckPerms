<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\context\calculator;

use jasonw4331\LuckPerms\api\context\Context;
use jasonw4331\LuckPerms\api\context\ContextConsumer;
use function mb_strtolower;

class NonEmptyWorldNameRewriter extends WorldNameRewriter{

	/**
	 * @param TypedMap<string, string> $rewrites
	 */
	public function __construct(private array $rewrites){ }

	public function rewriteAndSubmit(string $worldName, ContextConsumer $consumer) : void{
		$seen = [];
		$worldName = mb_strtolower($worldName);
		while(Context::isValidValue($worldName) && !in_array($worldName, $seen, true)){
			$seen[] = $worldName;
			$consumer->accept(DefaultContextKeys::WORLD_KEY(), $worldName);
			$worldName = $this->rewrites[$worldName] ?? null;
			if($worldName === null){
				break;
			}
		}
	}
}
