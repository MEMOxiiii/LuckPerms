<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\node;

/**
 * A simple named NodeEqualityPredicate that delegates to Node::equals().
 */
final class DummyNodeEqualityPredicate implements NodeEqualityPredicate
{
    public function __construct(private readonly string $name)
    {
    }

    public function areEqual(Node $o1, Node $o2): bool
    {
        return $o1->equals($o2, $this);
    }

    public function equalTo(Node $node): callable
    {
        return fn(Node $other): bool => $this->areEqual($node, $other);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
