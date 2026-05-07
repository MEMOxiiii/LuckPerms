<?php

declare(strict_types=1);

namespace muqsit\simplepackethandler;

use muqsit\simplepackethandler\interceptor\IPacketInterceptor;
use muqsit\simplepackethandler\interceptor\PacketInterceptor;
use muqsit\simplepackethandler\monitor\IPacketMonitor;
use muqsit\simplepackethandler\monitor\PacketMonitor;
use pocketmine\plugin\Plugin;

final class SimplePacketHandler{

	public static function createInterceptor(Plugin $registrant, int $priority = 0, bool $handleCancelled = false) : IPacketInterceptor{
		return new PacketInterceptor($registrant, $priority, $handleCancelled);
	}

	public static function createMonitor(Plugin $registrant, int $priority = 0, bool $handleCancelled = false) : IPacketMonitor{
		return new PacketMonitor($registrant, $priority, $handleCancelled);
	}
}
