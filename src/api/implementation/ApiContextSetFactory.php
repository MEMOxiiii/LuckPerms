<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

class ApiContextSetFactory{
	public function mutable() : \jasonw4331\LuckPerms\context\MutableContextSet{
		return new \jasonw4331\LuckPerms\context\MutableContextSetImpl();
	}

	public function immutableEmpty() : \jasonw4331\LuckPerms\context\ImmutableContextSet{
		return new \jasonw4331\LuckPerms\context\ImmutableContextSetImpl([]);
	}

	public function immutableOf(string $key, string $value) : \jasonw4331\LuckPerms\context\ImmutableContextSet{
		return new \jasonw4331\LuckPerms\context\ImmutableContextSetImpl([
			new \jasonw4331\LuckPerms\context\ContextImpl($key, $value),
		]);
	}

	public function immutableBuilder() : \jasonw4331\LuckPerms\context\ImmutableContextSetBuilder{
		return new class extends \jasonw4331\LuckPerms\context\ImmutableContextSetBuilder{
			private \jasonw4331\LuckPerms\context\MutableContextSetImpl $delegate;

			public function __construct(){
				$this->delegate = new \jasonw4331\LuckPerms\context\MutableContextSetImpl();
			}

			public function add(string $key, string $value) : \jasonw4331\LuckPerms\context\ImmutableContextSetBuilder{
				$this->delegate->add($key, $value);
				return $this;
			}

			public function addAllContexts(\jasonw4331\LuckPerms\context\ContextSet $contextSet) : \jasonw4331\LuckPerms\context\ImmutableContextSetBuilder{
				$this->delegate->addAllContexts($contextSet);
				return $this;
			}

			public function build() : \jasonw4331\LuckPerms\context\ImmutableContextSet{
				return $this->delegate->immutableCopy();
			}
		};
	}

}
