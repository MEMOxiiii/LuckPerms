<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\metastacking;

use jasonw4331\LuckPerms\api\metastacking\MetaStackElement;

final class FluentMetaStackElementBuilder{
	private array $elements = [];
	/** @var array<string, string> */
	private array $params = [];

	public function __construct(private string $name){ }

	public function with(MetaStackElement $element) : self{
		$this->elements[] = $element;
		return $this;
	}

	public function param(string $name, string $value) : self{
		$this->params[$name] = $value;
		return $this;
	}

	public function build() : MetaStackElement{
		return new FluentMetaStackElement($this->name, $this->params, $this->elements);
	}

}
