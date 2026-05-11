<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\filter;

/**
 * Pagination parameters for paginated query results.
 * Mirrors Java's PageParameters.
 */
class PageParameters{
	public const DEFAULT_PAGE = 1;
	public const DEFAULT_PAGE_SIZE = 15;

	private int $page;
	private int $pageSize;

	public function __construct(int $page = self::DEFAULT_PAGE, int $pageSize = self::DEFAULT_PAGE_SIZE){
		$this->page = max(1, $page);
		$this->pageSize = max(1, $pageSize);
	}

	public function getPage() : int{
		return $this->page;
	}

	public function getPageSize() : int{
		return $this->pageSize;
	}

	/** Returns the zero-based offset (for SQL OFFSET). */
	public function getOffset() : int{
		return ($this->page - 1) * $this->pageSize;
	}

	/** Calculates the total number of pages given a total record count. */
	public function getTotalPages(int $totalRecords) : int{
		if($totalRecords === 0){
			return 1;
		}
		return (int) ceil($totalRecords / $this->pageSize);
	}
}
