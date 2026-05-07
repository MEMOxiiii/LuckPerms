<?php

declare(strict_types=1);

namespace muqsit\simplepackethandler\monitor;

use pocketmine\plugin\Plugin;

class PacketMonitor implements IPacketMonitor{

	public function __construct(
		private Plugin $plugin,
		private int $priority = 0,
		private bool $handleCancelled = false
	){
	}

	public function monitorIncoming(callable $handler) : IPacketMonitor{
		return $this;
	}

	public function monitorOutgoing(callable $handler) : IPacketMonitor{
		return $this;
	}

	public function unregister() : void{
	}
}
