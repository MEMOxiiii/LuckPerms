<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\messaging\message\CustomMessageImpl;
use jasonw4331\LuckPerms\messaging\message\UpdateMessageImpl;
use jasonw4331\LuckPerms\messaging\message\UserUpdateMessageImpl;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function json_decode;
use function json_encode;
use function time;

/**
 * Core LuckPerms messaging service implementation.
 * Decodes incoming messages, deduplicates them, and dispatches the appropriate action.
 */
class LuckPermsMessagingService extends InternalMessagingService{
	/** @var array<string, int> UUID string => timestamp (for deduplication) */
	private array $receivedMessages = [];

	/** Expiry window for deduplication (5 minutes). */
	private const DEDUP_WINDOW_SECONDS = 300;

	public function __construct(private LuckPerms $plugin){ }

	/**
	 * Push a full-reload update message to all connected servers.
	 */
	public function pushUpdate() : void{
		$message = new UpdateMessageImpl();
		$this->sendOutgoing($message->toJsonArray());
	}

	/**
	 * Push a single-user update message.
	 */
	public function pushUserUpdate(UuidInterface $uuid) : void{
		$message = new UserUpdateMessageImpl(null, $uuid);
		$this->sendOutgoing($message->toJsonArray());
	}

	/**
	 * Push a custom channel message.
	 */
	public function pushCustomMessage(string $channelId, string $payload) : void{
		$message = new CustomMessageImpl(null, $channelId, $payload);
		$this->sendOutgoing($message->toJsonArray());
	}

	/**
	 * Handle an incoming raw JSON message string.
	 */
	public function consumeIncomingMessageAsString(string $encodedString) : bool{
		$data = json_decode($encodedString, true);
		if(!is_array($data)){
			return false;
		}
		return $this->consumeIncomingMessage($data);
	}

	/**
	 * Handle a decoded message payload.
	 *
	 * @param array $data
	 * @return bool true if the message was new (not a duplicate)
	 */
	public function consumeIncomingMessage(array $data) : bool{
		$idStr = $data['id'] ?? null;
		if($idStr === null){
			return false;
		}

		$this->cleanDedup();

		// Deduplicate
		if(isset($this->receivedMessages[$idStr])){
			return false;
		}
		$this->receivedMessages[$idStr] = time();

		$type = $data['type'] ?? '';
		switch($type){
			case UpdateMessageImpl::TYPE:
				$this->plugin->getScheduler()->scheduleTask(new \pocketmine\scheduler\ClosureTask(
					function() : void{ $this->plugin->runNetworkSync(); }
				));
				break;
			case UserUpdateMessageImpl::TYPE:
				$msg = UserUpdateMessageImpl::decode($data);
				if($msg !== null && $msg->getUserUuid() !== null){
					$uuid = $msg->getUserUuid();
					$this->plugin->getScheduler()->scheduleTask(new \pocketmine\scheduler\ClosureTask(
						function() use ($uuid) : void{ $this->plugin->runUserSync($uuid); }
					));
				}
				break;
			case CustomMessageImpl::TYPE:
				// Forward to any registered custom message consumers
				$msg = CustomMessageImpl::decode($data);
				if($msg !== null){
					$this->plugin->getEventBus()->post(new \jasonw4331\LuckPerms\event\gen\EventCustomMessageReceive(
						$msg->getChannelId(), $msg->getPayload() ?? ''
					));
				}
				break;
		}

		return true;
	}

	private function cleanDedup() : void{
		$threshold = time() - self::DEDUP_WINDOW_SECONDS;
		foreach($this->receivedMessages as $id => $ts){
			if($ts < $threshold){
				unset($this->receivedMessages[$id]);
			}
		}
	}

	/**
	 * Send an outgoing message payload via the configured messenger.
	 * Subclasses or the platform bridge should override/extend to actually transmit.
	 *
	 * @param array $payload
	 */
	protected function sendOutgoing(array $payload) : void{
		// Implementation depends on the active messenger (Redis, RabbitMQ, SQL, etc.)
		// The concrete MessagingFactory sets up the transport; here we just log.
		$this->plugin->getLogger()->debug('[Messaging] Outgoing: ' . json_encode($payload));
	}

	public function close() : void{
		// close any open connections
	}

	public function getName() : string{
		return 'LuckPermsMessaging';
	}
}

