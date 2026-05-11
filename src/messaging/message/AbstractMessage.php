<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging\message;

use Ramsey\Uuid\UuidInterface;

/**
 * Base class for all LuckPerms messaging messages.
 */
abstract class AbstractMessage{
	public function __construct(private UuidInterface $id){ }

	public function getId() : UuidInterface{
		return $this->id;
	}

	/** Serialize this message to a JSON-encodable array. */
	abstract public function toJsonArray() : array;
}

