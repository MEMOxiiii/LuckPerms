<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging\message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Message broadcasting an action-log entry to connected servers.
 */
class ActionLogMessageImpl extends AbstractMessage{
	public const TYPE = 'log';

	public function __construct(
		?UuidInterface $id = null,
		private ?array $logEntry = null
	){
		parent::__construct($id ?? Uuid::uuid4());
	}

	/** @return array|null the serialized log entry, or null */
	public function getLogEntry() : ?array{
		return $this->logEntry;
	}

	public function toJsonArray() : array{
		return [
			'type' => self::TYPE,
			'id' => $this->getId()->toString(),
			'logEntry' => $this->logEntry,
		];
	}

	public static function decode(array $data) : ?self{
		$id = isset($data['id']) ? Uuid::fromString($data['id']) : Uuid::uuid4();
		$logEntry = $data['logEntry'] ?? null;
		return new self($id, $logEntry);
	}
}
