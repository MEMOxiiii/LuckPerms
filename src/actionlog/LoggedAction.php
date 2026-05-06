<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\actionlog;

use function time;

/**
 * Represents a single logged action (permission change, group edit, etc.)
 */
class LoggedAction{
	public function __construct(
		private int    $timestamp,
		private string $actorUuid,
		private string $actorName,
		private string $targetType, // 'USER', 'GROUP', 'TRACK'
		private string $targetUuid,
		private string $targetName,
		private string $description
	){}

	public function getTimestamp() : int{ return $this->timestamp; }
	public function getActorUuid() : string{ return $this->actorUuid; }
	public function getActorName() : string{ return $this->actorName; }
	public function getTargetType() : string{ return $this->targetType; }
	public function getTargetUuid() : string{ return $this->targetUuid; }
	public function getTargetName() : string{ return $this->targetName; }
	public function getDescription() : string{ return $this->description; }

	public static function build(
		string $actorUuid,
		string $actorName,
		string $targetType,
		string $targetUuid,
		string $targetName,
		string $description
	) : self{
		return new self(time(), $actorUuid, $actorName, $targetType, $targetUuid, $targetName, $description);
	}
}
