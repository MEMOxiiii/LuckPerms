<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\query;

enum Flag: string
{
    /** If parent groups should be resolved */
    case RESOLVE_INHERITANCE = 'RESOLVE_INHERITANCE';

    /** If global or non-server-specific nodes should be applied */
    case INCLUDE_NODES_WITHOUT_SERVER_CONTEXT = 'INCLUDE_NODES_WITHOUT_SERVER_CONTEXT';

    /** If global or non-world-specific nodes should be applied */
    case INCLUDE_NODES_WITHOUT_WORLD_CONTEXT = 'INCLUDE_NODES_WITHOUT_WORLD_CONTEXT';

    /** If global or non-server-specific group memberships should be applied */
    case APPLY_INHERITANCE_NODES_WITHOUT_SERVER_CONTEXT = 'APPLY_INHERITANCE_NODES_WITHOUT_SERVER_CONTEXT';

    /** If global or non-world-specific group memberships should be applied */
    case APPLY_INHERITANCE_NODES_WITHOUT_WORLD_CONTEXT = 'APPLY_INHERITANCE_NODES_WITHOUT_WORLD_CONTEXT';
}
