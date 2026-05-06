<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inject\permissible;

use pocketmine\player\Player;

class PermissibleInjector{

	public function __construct(\Closure $param){ }

	public static function inject(Player $player, mixed $permissible) : void{
		// stub — permissible injection not implemented for PM5
	}

	public static function uninject(Player $player, bool $runCallbacks) : void{
		// stub — permissible uninject not implemented for PM5
	}
}
