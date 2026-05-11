<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\model\data;

use jasonw4331\LuckPerms\api\node\Node;

/**
 * Extension of DataMutateResult for temporary set operations.
 */
interface DataMutateResultWithMergedNode
{
    public function getResult(): DataMutateResult;

    public function getMergedNode(): Node;
}
