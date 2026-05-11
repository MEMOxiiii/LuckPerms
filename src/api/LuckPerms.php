<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api;

use jasonw4331\LuckPerms\api\actionlog\ActionLogger;
use jasonw4331\LuckPerms\api\actionlog\filter\ActionFilterFactory;
use jasonw4331\LuckPerms\api\context\ContextManager;
use jasonw4331\LuckPerms\api\event\EventBus;
use jasonw4331\LuckPerms\api\messaging\MessagingService;
use jasonw4331\LuckPerms\api\messenger\MessengerProvider;
use jasonw4331\LuckPerms\api\model\group\GroupManager;
use jasonw4331\LuckPerms\api\model\user\UserManager;
use jasonw4331\LuckPerms\api\node\matcher\NodeMatcherFactory;
use jasonw4331\LuckPerms\api\platform\Health;
use jasonw4331\LuckPerms\api\platform\Platform;
use jasonw4331\LuckPerms\api\platform\PlayerAdapter;
use jasonw4331\LuckPerms\api\platform\PluginMetadata;
use jasonw4331\LuckPerms\api\query\QueryOptionsRegistry;
use jasonw4331\LuckPerms\api\track\TrackManager;

/**
 * The LuckPerms API.
 *
 * The API allows other plugins on the server to read and modify LuckPerms data.
 */
interface LuckPerms
{
    public function getServerName(): string;

    public function getUserManager(): UserManager;

    public function getGroupManager(): GroupManager;

    public function getTrackManager(): TrackManager;

    /**
     * Gets the PlayerAdapter for the given player class name.
     * In PHP (PocketMine), pass the FQCN of the player class.
     */
    public function getPlayerAdapter(string $playerClass): PlayerAdapter;

    public function getPlatform(): Platform;

    public function getPluginMetadata(): PluginMetadata;

    public function getEventBus(): EventBus;

    public function getMessagingService(): ?MessagingService;

    public function getActionLogger(): ActionLogger;

    public function getContextManager(): ContextManager;

    public function getNodeBuilderRegistry(): NodeBuilderRegistry;

    public function getQueryOptionsRegistry(): QueryOptionsRegistry;

    public function getNodeMatcherFactory(): NodeMatcherFactory;

    public function getActionFilterFactory(): ActionFilterFactory;

    /**
     * Schedules the execution of an update task, causing all data to be reloaded.
     */
    public function runUpdateTask(): void;

    /**
     * Runs a health check on LuckPerms.
     */
    public function runHealthCheck(): Health;

    /**
     * Registers a messenger provider with LuckPerms.
     */
    public function registerMessengerProvider(MessengerProvider $messengerProvider): void;
}
