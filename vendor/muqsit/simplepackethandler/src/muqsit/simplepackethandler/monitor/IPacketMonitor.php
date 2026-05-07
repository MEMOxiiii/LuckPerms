<?php

declare(strict_types=1);

namespace muqsit\simplepackethandler\monitor;

interface IPacketMonitor{

	public function monitorIncoming(callable $handler) : IPacketMonitor;

	public function monitorOutgoing(callable $handler) : IPacketMonitor;

	public function unregister() : void;
}
