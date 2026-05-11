<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\cacheddata;

use jasonw4331\LuckPerms\api\node\types\MetaNode;
use jasonw4331\LuckPerms\api\node\types\PrefixNode;
use jasonw4331\LuckPerms\api\node\types\SuffixNode;
use jasonw4331\LuckPerms\api\node\types\WeightNode;

/**
 * Holds cached meta lookup data for a specific set of contexts.
 */
interface CachedMetaData extends CachedData
{
    public function queryMetaValue(string $key): Result;

    public function getMetaValue(string $key): ?string;

    public function getMetaValueTransformed(string $key, callable $valueTransformer): mixed;

    public function queryPrefix(): Result;

    public function getPrefix(): ?string;

    public function querySuffix(): Result;

    public function getSuffix(): ?string;

    public function queryWeight(): Result;

    public function getWeight(): int;

    /**
     * Gets all meta values mapped by key, with highest priority last.
     *
     * @return array<int, array<string, string>>
     */
    public function getMeta(): array;

    /**
     * Gets all prefix entries as a sorted map, priority -> prefix string.
     *
     * @return array<int, string>
     */
    public function getPrefixes(): array;

    /**
     * Gets all suffix entries as a sorted map, priority -> suffix string.
     *
     * @return array<int, string>
     */
    public function getSuffixes(): array;
}
