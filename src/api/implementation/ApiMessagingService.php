<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\messaging\MessagingService as MessagingServiceInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\messaging\LuckPermsMessagingService;
use Ramsey\Uuid\UuidInterface;

class ApiMessagingService extends ApiAbstractManager implements MessagingServiceInterface{
private LuckPermsMessagingService $messagingService;

public function __construct(LuckPerms $plugin, LuckPermsMessagingService $messagingService){
parent::__construct($plugin);
$this->messagingService = $messagingService;
}

public function getName() : string{
return $this->messagingService->getName();
}

public function pushUpdate() : void{
$this->messagingService->pushUpdate();
}

public function pushUserUpdate(UuidInterface $uuid) : void{
$this->messagingService->pushUserUpdate($uuid);
}

public function sendCustomMessage(string $channelId, string $payload) : void{
$this->messagingService->pushCustomMessage($channelId, $payload);
}
}
