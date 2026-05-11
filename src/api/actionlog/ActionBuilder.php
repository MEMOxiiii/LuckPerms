<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog;

use Ramsey\Uuid\UuidInterface;

/**
 * Builder for Action instances.
 */
interface ActionBuilder
{
	public function timestamp(\DateTimeImmutable $timestamp) : self;

	public function source(UuidInterface $actor) : self;

	public function sourceName(string $actorName) : self;

	public function targetType(ActionTargetType $type) : self;

	public function target(?UuidInterface $acted) : self;

	public function targetName(string $actedName) : self;

	public function description(string $action) : self;

	public function build() : Action;
}
