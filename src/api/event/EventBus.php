<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\event;

/**
 * The EventBus is responsible for subscribing to and publishing LuckPerms events.
 */
interface EventBus
{
	/**
	 * Subscribe to an event.
	 *
	 * @param string   $eventClass fully-qualified class name of the LuckPermsEvent
	 * @param callable $handler    invoked when the event fires; receives the event as argument
	 */
	public function subscribe(string $eventClass, callable $handler) : EventSubscription;

	/**
	 * Subscribe to an event, binding the subscription to a plugin instance.
	 */
	public function subscribeWithPlugin(object $plugin, string $eventClass, callable $handler) : EventSubscription;

	/**
	 * @return EventSubscription[]
	 */
	public function getSubscriptions(string $eventClass) : array;
}
