<?php

declare(strict_types=1);

namespace muqsit\simplepackethandler\interceptor;

use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

/**
 * Stub implementation of IPacketInterceptor using PocketMine's event system.
 * Intercepts outgoing packets by listening to DataPacketSendEvent.
 */
class PacketInterceptor implements IPacketInterceptor{

	/** @var callable[] */
	private array $outgoingHandlers = [];
	/** @var callable[] */
	private array $incomingHandlers = [];

	private bool $registered = false;

	public function __construct(
		private Plugin $plugin,
		private int $priority = EventPriority::NORMAL,
		private bool $handleCancelled = false
	){
	}

	public function interceptIncoming(callable $handler) : IPacketInterceptor{
		$this->incomingHandlers[] = $handler;
		return $this;
	}

	public function interceptOutgoing(callable $handler) : IPacketInterceptor{
		$this->outgoingHandlers[] = $handler;
		if(!$this->registered){
			$this->registerListener();
		}
		return $this;
	}

	private function registerListener() : void{
		$this->registered = true;
		$handlers = &$this->outgoingHandlers;
		// Cache handler packet types via reflection for performance
		$handlerTypes = [];
		Server::getInstance()->getPluginManager()->registerEvent(
			DataPacketSendEvent::class,
			function(DataPacketSendEvent $event) use (&$handlers, &$handlerTypes) : void{
				foreach($event->getPackets() as $pk){
					$targets = $event->getTargets();
					foreach($targets as $target){
						foreach($handlers as $idx => $handler){
							// Determine expected packet type from handler's first parameter
							if(!isset($handlerTypes[$idx])){
								try{
									$rf = new \ReflectionFunction(\Closure::fromCallable($handler));
									$params = $rf->getParameters();
									$handlerTypes[$idx] = (isset($params[0]) && ($t = $params[0]->getType()) !== null && $t instanceof \ReflectionNamedType)
										? $t->getName()
										: null;
								}catch(\Throwable $e){
									$handlerTypes[$idx] = null;
								}
							}
							$expectedType = $handlerTypes[$idx];
							if($expectedType !== null && !($pk instanceof $expectedType)){
								continue;
							}
							$handler($pk, $target);
						}
					}
				}
			},
			$this->priority,
			$this->plugin,
			$this->handleCancelled
		);
	}

	public function unregister() : void{
		$this->outgoingHandlers = [];
		$this->incomingHandlers = [];
	}
}
