<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query;

use jasonw4331\LuckPerms\api\context\ContextSet;

/**
 * Builder for QueryOptions.
 */
interface QueryOptionsBuilder
{
    /**
     * Sets the QueryMode.
     */
    public function mode(QueryMode $mode): self;

    /**
     * Sets the context.
     */
    public function context(ContextSet $context): self;

    /**
     * Sets the value of the given flag.
     */
    public function flag(Flag $flag, bool $value): self;

    /**
     * Sets the flags.
     *
     * @param Flag[] $flags
     */
    public function flags(array $flags): self;

    /**
     * Sets the value of a given option key.
     */
    public function option(OptionKey $key, mixed $value): self;

    /**
     * Builds the QueryOptions.
     */
    public function build(): QueryOptions;
}
