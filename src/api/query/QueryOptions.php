<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query;

use jasonw4331\LuckPerms\api\context\ContextSatisfyMode;
use jasonw4331\LuckPerms\api\context\ContextSet;
use jasonw4331\LuckPerms\api\context\ImmutableContextSet;

/**
 * Represents the parameters for a lookup query.
 */
interface QueryOptions
{
    /**
     * Gets the QueryMode.
     */
    public function mode(): QueryMode;

    /**
     * Gets the context, if the options are contextual.
     * Throws \RuntimeException if the mode is NON_CONTEXTUAL.
     */
    public function context(): ImmutableContextSet;

    /**
     * Gets if the given Flag is set.
     */
    public function flag(Flag $flag): bool;

    /**
     * Gets the Flags which are set.
     *
     * @return Flag[]
     */
    public function flags(): array;

    /**
     * Gets the value assigned to the given OptionKey.
     * Returns null if the option has not been set.
     */
    public function option(OptionKey $key): mixed;

    /**
     * Gets the options which are set.
     *
     * @return array<mixed>
     */
    public function options(): array;

    /**
     * Gets whether this QueryOptions satisfies the given required context set.
     */
    public function satisfies(ContextSet $contextSet, ?ContextSatisfyMode $defaultContextSatisfyMode = null): bool;

    /**
     * Converts this QueryOptions to a mutable builder.
     */
    public function toBuilder(): QueryOptionsBuilder;
}
