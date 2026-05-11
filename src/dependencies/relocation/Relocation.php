<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\dependencies\relocation;

class Relocation{
public function __construct(
private string $pattern,
private string $relocatedPattern
){}

public function getPattern() : string{ return $this->pattern; }
public function getRelocatedPattern() : string{ return $this->relocatedPattern; }
}
