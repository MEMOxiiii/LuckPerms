<?php

declare(strict_types=1);

namespace muqsit\simplepackethandler\interceptor;

interface IPacketInterceptor{

	/**
	 * @param callable $handler fn(Packet, NetworkSession) : bool
	 * @return IPacketInterceptor
	 */
	public function interceptIncoming(callable $handler) : IPacketInterceptor;

	/**
	 * @param callable $handler fn(Packet, NetworkSession) : bool
	 * @return IPacketInterceptor
	 */
	public function interceptOutgoing(callable $handler) : IPacketInterceptor;

	public function unregister() : void;
}
