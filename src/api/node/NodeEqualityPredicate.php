<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\node;

/**
 * Determines equality between two Node instances.
 */
interface NodeEqualityPredicate
{
	public function areEqual(Node $o1, Node $o2) : bool;

	/**
	 * Returns a callable that checks if a given node equals $node per this predicate.
	 *
	 * @return callable(Node): bool
	 */
	public function equalTo(Node $node) : callable;
}
