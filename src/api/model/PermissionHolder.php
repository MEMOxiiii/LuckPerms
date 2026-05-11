<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model;

use jasonw4331\LuckPerms\api\cacheddata\CachedDataManager;
use jasonw4331\LuckPerms\api\model\data\DataType;
use jasonw4331\LuckPerms\api\model\data\NodeMap;
use jasonw4331\LuckPerms\api\node\Node;
use jasonw4331\LuckPerms\api\node\NodeType;
use jasonw4331\LuckPerms\api\query\QueryOptions;

/**
 * Generic superinterface for an object which holds permissions.
 */
interface PermissionHolder
{
    /**
     * Represents a way to identify distinct PermissionHolders.
     */
    interface Identifier
    {
        public const USER_TYPE = 'user';
        public const GROUP_TYPE = 'group';

        public function getName(): string;
        public function getType(): string;
    }

    public function getIdentifier(): Identifier;

    public function getFriendlyName(): string;

    public function getQueryOptions(): QueryOptions;

    public function getCachedData(): CachedDataManager;

    public function getData(DataType $dataType): NodeMap;

    public function data(): NodeMap;

    public function transientData(): NodeMap;

    /**
     * @return Node[]
     */
    public function getNodes(): array;

    /**
     * @return Node[]
     */
    public function getNodesByType(NodeType $type): array;

    /**
     * @return Node[]
     */
    public function getDistinctNodes(): array;

    /**
     * @return Node[]
     */
    public function resolveInheritedNodes(QueryOptions $queryOptions): array;

    /**
     * @return Node[]
     */
    public function resolveInheritedNodesByType(NodeType $type, QueryOptions $queryOptions): array;

    /**
     * @return Node[]
     */
    public function resolveDistinctInheritedNodes(QueryOptions $queryOptions): array;

    /**
     * @return \jasonw4331\LuckPerms\api\model\group\Group[]
     */
    public function getInheritedGroups(QueryOptions $queryOptions): array;
}
