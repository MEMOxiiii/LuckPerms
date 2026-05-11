<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\actionlog;

use jasonw4331\LuckPerms\api\actionlog\filter\ActionFilter;
use jasonw4331\LuckPerms\api\util\Page;

/**
 * Handles the submission and retrieval of logged actions.
 */
interface ActionLogger
{
    public function actionBuilder(): ActionBuilder;

    /**
     * @deprecated Use queryActions() instead.
     */
    public function getLog(): ActionLog;

    /**
     * @return Action[]
     */
    public function queryActions(ActionFilter $filter): array;

    public function queryActionsPage(ActionFilter $filter, int $pageSize, int $pageNumber): Page;

    public function submit(Action $entry): void;

    public function submitToStorage(Action $entry): void;

    public function broadcastAction(Action $entry): void;
}
