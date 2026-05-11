<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\inheritance;

use jasonw4331\LuckPerms\config\ConfigKeys;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\query\QueryOptions;

/**
 * Factory that creates {@link InheritanceGraph} instances.
 */
class InheritanceGraphFactory{
	public function __construct(private LuckPerms $plugin){ }

	/**
	 * Creates an InheritanceGraph using the algorithm defined in the plugin config.
	 */
	public function getGraph(?QueryOptions $queryOptions = null) : InheritanceGraph{
		$config = $this->plugin->getConfiguration();
		$algorithm = $config->get(ConfigKeys::INHERITANCE_TRAVERSAL_ALGORITHM());
		$postSort = (bool) $config->get(ConfigKeys::POST_TRAVERSAL_INHERITANCE_SORT());
		$groupManager = $this->plugin->getGroupManager();
		return new InheritanceGraph(
			$algorithm,
			$postSort,
			static fn(string $name) => $groupManager->getIfLoaded($name)
		);
	}
}

