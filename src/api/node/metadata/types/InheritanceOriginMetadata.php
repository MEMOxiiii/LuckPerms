<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\node\metadata\types;

use jasonw4331\LuckPerms\api\model\data\DataType;
use jasonw4331\LuckPerms\api\model\PermissionHolder;
use jasonw4331\LuckPerms\api\node\metadata\NodeMetadataKey;

/**
 * Metadata attached to an inherited node, indicating where it was inherited from.
 */
interface InheritanceOriginMetadata
{
    /**
     * The metadata key for InheritanceOriginMetadata.
     */
    public static function key(): NodeMetadataKey;

    public function getOrigin(): PermissionHolder\Identifier;

    public function getDataType(): DataType;

    public function wasInherited(PermissionHolder\Identifier $holder): bool;
}
