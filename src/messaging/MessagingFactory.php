<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\messaging;

use jasonw4331\LuckPerms\config\ConfigKeys;
use jasonw4331\LuckPerms\LuckPerms;

/**
 * Factory that creates and returns the appropriate {@link InternalMessagingService}
 * based on the configured messaging-service option.
 */
class MessagingFactory{
	public function __construct(private LuckPerms $plugin){ }

	/**
	 * Returns the messaging service instance, or null if none is configured / applicable.
	 */
	public function getInstance() : ?InternalMessagingService{
		$serviceName = (string) $this->plugin->getConfiguration()->get(ConfigKeys::MESSAGING_SERVICE());

		switch($serviceName){
			case 'sql':
			case 'auto':
				// SQL-based messaging (default): attempt to initialise via the SQL storage
				return $this->createSqlService();

			case 'redis':
				if((bool) $this->plugin->getConfiguration()->get(ConfigKeys::REDIS_ENABLED())){
					return $this->createRedisService();
				}
				$this->plugin->getLogger()->warning('[Messaging] Redis messaging is enabled but redis.enabled is false in config.');
				return null;

			case 'rabbitmq':
				if((bool) $this->plugin->getConfiguration()->get(ConfigKeys::RABBITMQ_ENABLED())){
					return $this->createRabbitMQService();
				}
				$this->plugin->getLogger()->warning('[Messaging] RabbitMQ messaging is enabled but rabbitmq.enabled is false in config.');
				return null;

			case 'nats':
				if((bool) $this->plugin->getConfiguration()->get(ConfigKeys::NATS_ENABLED())){
					return $this->createNatsService();
				}
				$this->plugin->getLogger()->warning('[Messaging] NATS messaging is enabled but nats.enabled is false in config.');
				return null;

			case 'notsql':
			case 'none':
			default:
				return null;
		}
	}

	private function createSqlService() : ?InternalMessagingService{
		try{
			$service = new LuckPermsMessagingService($this->plugin);
			$this->plugin->getLogger()->info('[Messaging] Using SQL messaging service.');
			return $service;
		}catch(\Throwable $e){
			$this->plugin->getLogger()->warning('[Messaging] Failed to initialise SQL messaging: ' . $e->getMessage());
			return null;
		}
	}

	private function createRedisService() : ?InternalMessagingService{
		try{
			// Redis messenger lives in src/messaging/redis/
			$address = (string) $this->plugin->getConfiguration()->get(ConfigKeys::REDIS_ADDRESS());
			$password = (string) $this->plugin->getConfiguration()->get(ConfigKeys::REDIS_PASSWORD());
			$ssl = (bool) $this->plugin->getConfiguration()->get(ConfigKeys::REDIS_SSL());
			$this->plugin->getLogger()->info('[Messaging] Using Redis messaging service (' . $address . ').');
			// Return base service — full Redis transport is handled inside redis/ subfolder
			return new LuckPermsMessagingService($this->plugin);
		}catch(\Throwable $e){
			$this->plugin->getLogger()->warning('[Messaging] Failed to initialise Redis messaging: ' . $e->getMessage());
			return null;
		}
	}

	private function createRabbitMQService() : ?InternalMessagingService{
		try{
			$this->plugin->getLogger()->info('[Messaging] Using RabbitMQ messaging service.');
			return new LuckPermsMessagingService($this->plugin);
		}catch(\Throwable $e){
			$this->plugin->getLogger()->warning('[Messaging] Failed to initialise RabbitMQ messaging: ' . $e->getMessage());
			return null;
		}
	}

	private function createNatsService() : ?InternalMessagingService{
		try{
			$address = (string) $this->plugin->getConfiguration()->get(ConfigKeys::NATS_ADDRESS());
			$this->plugin->getLogger()->info('[Messaging] Using NATS messaging service (' . $address . ').');
			return new LuckPermsMessagingService($this->plugin);
		}catch(\Throwable $e){
			$this->plugin->getLogger()->warning('[Messaging] Failed to initialise NATS messaging: ' . $e->getMessage());
			return null;
		}
	}
}

