<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\context;

/**
 * Calculates the active contexts for a subject.
 */
interface ContextCalculator
{
    /**
     * Appends active contexts for the given subject into the consumer.
     */
    public function calculate(mixed $target, ContextConsumer $consumer): void;

    /**
     * Returns a set of contexts that could potentially be active for this calculator.
     * Optional, can return an empty set.
     */
    public function estimatePotentialContexts(): ImmutableContextSet;
}
