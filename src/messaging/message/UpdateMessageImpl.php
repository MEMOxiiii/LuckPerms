<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging\message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Message requesting all servers to reload their data from storage.
 */
class UpdateMessageImpl extends AbstractMessage{
	public const TYPE = 'update';

	public function __construct(?UuidInterface $id = null){
		parent::__construct($id ?? Uuid::uuid4());
	}

	public function toJsonArray() : array{
		return [
			'type' => self::TYPE,
			'id' => $this->getId()->toString(),
		];
	}

	public static function decode(array $data) : ?self{
		$id = isset($data['id']) ? Uuid::fromString($data['id']) : Uuid::uuid4();
		return new self($id);
	}
}
