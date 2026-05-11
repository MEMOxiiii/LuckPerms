<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging\message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Message requesting a specific user’s data to be reloaded.
 */
class UserUpdateMessageImpl extends AbstractMessage{
	public const TYPE = 'user-update';

	public function __construct(?UuidInterface $id = null, private ?UuidInterface $userUuid = null){
		parent::__construct($id ?? Uuid::uuid4());
	}

	public function getUserUuid() : ?UuidInterface{
		return $this->userUuid;
	}

	public function toJsonArray() : array{
		return [
			'type' => self::TYPE,
			'id' => $this->getId()->toString(),
			'userUuid' => $this->userUuid?->toString(),
		];
	}

	public static function decode(array $data) : ?self{
		$id = isset($data['id']) ? Uuid::fromString($data['id']) : Uuid::uuid4();
		$userUuid = isset($data['userUuid']) ? Uuid::fromString($data['userUuid']) : null;
		return new self($id, $userUuid);
	}
}

