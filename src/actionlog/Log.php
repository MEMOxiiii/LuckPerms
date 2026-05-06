<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\actionlog;

use function array_filter;
use function array_slice;
use function array_values;
use function count;
use function strtolower;

/**
 * In-memory circular log holding recent actions (max 500 entries).
 */
class Log{
	private const MAX_ENTRIES = 500;

	/** @var LoggedAction[] */
	private array $entries = [];

	public function add(LoggedAction $action) : void{
		$this->entries[] = $action;
		if(count($this->entries) > self::MAX_ENTRIES){
			$this->entries = array_slice($this->entries, -self::MAX_ENTRIES);
		}
	}

	/** @return LoggedAction[] */
	public function getAll() : array{
		return $this->entries;
	}

	/** @return LoggedAction[] */
	public function getRecent(int $limit = 50) : array{
		return array_slice($this->entries, -$limit);
	}

	/** @return LoggedAction[] */
	public function searchByUser(string $username) : array{
		$lower = strtolower($username);
		return array_values(array_filter($this->entries, static fn(LoggedAction $a) =>
			strtolower($a->getTargetName()) === $lower && strtolower($a->getTargetType()) === 'user'
		));
	}

	/** @return LoggedAction[] */
	public function searchByGroup(string $group) : array{
		$lower = strtolower($group);
		return array_values(array_filter($this->entries, static fn(LoggedAction $a) =>
			strtolower($a->getTargetName()) === $lower && strtolower($a->getTargetType()) === 'group'
		));
	}
}
