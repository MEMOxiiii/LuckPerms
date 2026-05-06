<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\listeners;

use jasonw4331\LuckPerms\inject\permissible\PermissionHelper;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\node\NodeEntry;
use jasonw4331\LuckPerms\util\AbstractConnectionListener;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\ClosureTask;
use function strtolower;

class ConnectionListener extends AbstractConnectionListener implements Listener{

	public function __construct(LuckPerms $plugin){
		parent::__construct($plugin);
	}

	public function onPlayerLogin(PlayerLoginEvent $event) : void{
		$player = $event->getPlayer();
		$this->plugin->getScheduler()->scheduleTask(new ClosureTask(function() use ($player) : void{
			if(!$player->isConnected()) return;
			try{
				// Try to load existing user data from storage first
				$existingUser = $this->plugin->getStorage()->loadUser($player->getUniqueId());
				if($existingUser !== null){
					// Update username in case it changed
					$user = $this->plugin->getUserManager()->load($player->getUniqueId(), $player->getName());
					$user->setNodes($existingUser->getNodes());
				}else{
					$user = $this->loadUser($player->getUniqueId(), $player->getName());
					// New user: assign to default group if it exists
					if($user !== null){
						$defaultGroupKey = 'group.default';
						$hasDefault = false;
						foreach($user->getNodes() as $node){
							if(strtolower($node->getKey()) === $defaultGroupKey){
								$hasDefault = true;
								break;
							}
						}
						if(!$hasDefault){
							// Ensure 'default' group exists
							$this->plugin->getGroupManager()->getOrMake('default');
							$user->addNode(new NodeEntry($defaultGroupKey, true, [], null));
						}
					}
				}
				if($user !== null){
					// Save with updated username
					$this->plugin->getStorage()->saveUser($user);
					// Apply permissions via PermissionAttachment
					PermissionHelper::applyPermissions($player, $user, $this->plugin);
				}
			}catch(\Throwable $t){
				$this->plugin->getLogger()->error('Exception loading user data for ' . $player->getName() . ': ' . $t->getMessage());
			}
		}));
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		$user = $this->plugin->getUserManager()->getIfLoaded($player->getUniqueId());
		if($user !== null){
			$this->plugin->getStorage()->saveUser($user);
			$this->plugin->getUserManager()->unload($player->getUniqueId());
		}
		PermissionHelper::clearPermissions($player, $this->plugin);
	}
}
