<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging\message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * A custom messaging channel message.
 * Allows plugins/extensions to send arbitrary JSON payloads via LuckPerms messaging.
 */
class CustomMessageImpl extends AbstractMessage{
	public const TYPE = 'custom';

	public function __construct(
		?UuidInterface $id = null,
		private string $channelId = '',
		private ?string $payload = null
	){
		parent::__construct($id ?? Uuid::uuid4());
	}

	public function getChannelId() : string{
		return $this->channelId;
	}

	public function getPayload() : ?string{
		return $this->payload;
	}

	public function toJsonArray() : array{
		return [
			'type' => self::TYPE,
			'id' => $this->getId()->toString(),
			'channelId' => $this->channelId,
			'payload' => $this->payload,
		];
	}

	public static function decode(array $data) : ?self{
		$id = isset($data['id']) ? Uuid::fromString($data['id']) : Uuid::uuid4();
		$channelId = $data['channelId'] ?? '';
		$payload = $data['payload'] ?? null;
		return new self($id, $channelId, $payload);
	}
}
