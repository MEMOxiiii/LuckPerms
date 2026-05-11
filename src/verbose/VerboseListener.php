<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\verbose;

use jasonw4331\LuckPerms\sender\Sender;
use jasonw4331\LuckPerms\verbose\event\MetaCheckEvent;
use jasonw4331\LuckPerms\verbose\event\PermissionCheckEvent;
use jasonw4331\LuckPerms\verbose\event\VerboseEvent;
use function array_filter;
use function count;
use function method_exists;
use function microtime;

/**
 * Accepts and processes {@link VerboseEvent} objects passed from {@link VerboseHandler}.
 *
 * Each listener is bound to a {@link Sender} who will receive notifications,
 * and to a {@link VerboseFilter} that controls which events are recorded.
 */
class VerboseListener{
	private const DATE_FORMAT = 'Y-m-d H:i:s T';

	/** Maximum number of events to store before truncating. */
	private const DATA_TRUNCATION = 10_000;
	/** Stack trace lines sent in-chat. */
	private const STACK_TRUNCATION_CHAT = 15;
	/** Stack trace lines sent to web viewer. */
	private const STACK_TRUNCATION_WEB = 40;

	/** Rate-limit: max notifications per window per player. */
	private const NOTIFICATION_RATE_LIMIT_MAX_EVENTS_PLAYER = 50;
	/** Rate-limit: max notifications per window for console. */
	private const NOTIFICATION_RATE_LIMIT_MAX_EVENTS_CONSOLE = 100;
	private const NOTIFICATION_RATE_LIMIT_WINDOW_MS = 1000;

	/** Timestamp when this listener was first registered (ms). */
	private int $startTime;
	/** The sender to notify on each matched event. */
	private Sender $notifiedSender;
	/** The filter governing which events are recorded. */
	private VerboseFilter $filter;
	/** Whether to send real-time notifications to the sender. */
	private bool $notify;

	/** Total events seen (before filter). */
	private int $counter = 0;
	/** Events that passed the filter. */
	private int $matchedCounter = 0;
	/** @var VerboseEvent[] stored events (up to DATA_TRUNCATION) */
	private array $results = [];

	/** @var list<float> timestamps of recent notifications for rate-limiting */
	private array $notificationTimestamps = [];

	public function __construct(Sender $notifiedSender, VerboseFilter $filter, bool $notify){
		$this->notifiedSender = $notifiedSender;
		$this->filter = $filter;
		$this->notify = $notify;
		$this->startTime = (int) (microtime(true) * 1000);
	}

	/**
	 * Accept and process a verbose event.
	 */
	public function acceptEvent(VerboseEvent $event) : void{
		$this->counter++;

		if(!$this->filter->evaluate($event)){
			return;
		}

		$this->matchedCounter++;

		if(count($this->results) < self::DATA_TRUNCATION){
			$this->results[] = $event;
		}

		if($this->notify){
			$this->sendNotification($event);
		}
	}

	private function sendNotification(VerboseEvent $event) : void{
		if(!$this->checkNotificationRateLimit()){
			return;
		}

		if($event instanceof PermissionCheckEvent){
			$msg = '[Verbose] ' . $event->getCheckTarget()->describe()
				. ' | ' . $event->getPermission()
				. ' -> ' . ($event->getResult()->toBoolean() ? 'true' : (method_exists($event->getResult(), 'name') ? $event->getResult()->name() : 'undefined'));
		} elseif($event instanceof MetaCheckEvent){
			$msg = '[Verbose] ' . $event->getCheckTarget()->describe()
				. ' | meta:' . $event->getKey()
				. ' -> ' . ($event->getResult()->result() ?? 'null');
		} else{
			$msg = '[Verbose] ' . $event->getCheckTarget()->describe();
		}

		$this->notifiedSender->sendMessage($msg);
	}

	private function checkNotificationRateLimit() : bool{
		$now = microtime(true) * 1000;
		$windowStart = $now - self::NOTIFICATION_RATE_LIMIT_WINDOW_MS;

		$this->notificationTimestamps = array_filter(
			$this->notificationTimestamps,
			static fn(float $t) => $t >= $windowStart
		);

		$limit = $this->notifiedSender->isConsole()
			? self::NOTIFICATION_RATE_LIMIT_MAX_EVENTS_CONSOLE
			: self::NOTIFICATION_RATE_LIMIT_MAX_EVENTS_PLAYER;

		if(count($this->notificationTimestamps) >= $limit){
			return false;
		}

		$this->notificationTimestamps[] = $now;
		return true;
	}

	public function getCounter() : int{
		return $this->counter;
	}

	public function getMatchedCounter() : int{
		return $this->matchedCounter;
	}

	/** @return VerboseEvent[] */
	public function getResults() : array{
		return $this->results;
	}

	public function getFilter() : VerboseFilter{
		return $this->filter;
	}

	public function getNotifiedSender() : Sender{
		return $this->notifiedSender;
	}

	public function getStartTime() : int{
		return $this->startTime;
	}
}
