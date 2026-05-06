<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\misc;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\node\NodeEntry;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use function array_slice;
use function ceil;
use function count;
use function file_get_contents;
use function fnmatch;
use function is_array;
use function is_dir;
use function is_int;
use function is_numeric;
use function is_string;
use function json_decode;
use function max;
use function min;
use function scandir;
use function str_contains;
use function str_ends_with;
use function strtolower;
use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;

class SearchCommand extends BaseSubCommand{

	protected function prepare() : void{
		$this->setPermission('luckperms.search');
		$this->registerArgument(0, new RawStringArgument('query', false));
		$this->registerArgument(1, new RawStringArgument('page', true));
	}

	/** @param array<mixed> $args */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$query = isset($args['query']) && is_string($args['query']) ? $args['query'] : '';
		$page = isset($args['page']) && is_numeric($args['page']) ? (int) $args['page'] : 1;

		if($query === ''){
			$sender->sendMessage(TF::RED . 'Usage: /' . $aliasUsed . ' search <node|wildcard> [page]');
			$sender->sendMessage(TF::GRAY . 'Examples: search luckperms.* | search group.admin | search -group.*');
			return;
		}

		$plugin = LuckPerms::getInstance();
		$results = [];

		// Search in groups
		foreach($plugin->getGroupManager()->getAll() as $group){
			foreach($group->getNodes() as $node){
				if($this->matchesQuery($node->getKey(), $query)){
					$results[] = [
						'type' => 'group',
						'name' => $group->getName(),
						'node' => $node,
					];
				}
			}
		}

		// Search in all loaded users
		foreach($plugin->getUserManager()->getAll() as $user){
			foreach($user->getNodes() as $node){
				if($this->matchesQuery($node->getKey(), $query)){
					$results[] = [
						'type' => 'user',
						'name' => $user->getUsername(),
						'node' => $node,
					];
				}
			}
		}

		// Also scan user JSON files on disk (offline players)
		$userDir = $plugin->getDataFolder() . 'users' . DIRECTORY_SEPARATOR;
		if(is_dir($userDir)){
			$scanResult = scandir($userDir);
			foreach($scanResult !== false ? $scanResult : [] as $file){
				if(!str_ends_with($file, '.json')) continue;
				try{
					$content = file_get_contents($userDir . $file);
					if($content === false) continue;
					/** @var array<string,mixed> $data */
					$data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
					$uuidStr = is_string($data['uniqueId'] ?? null) ? $data['uniqueId'] : '';
					// skip already loaded users
					$alreadyLoaded = false;
					foreach($plugin->getUserManager()->getAll() as $u){
						if($u->getUniqueId()->toString() === $uuidStr){ $alreadyLoaded = true; break; }
					}
					if($alreadyLoaded) continue;
					$username = is_string($data['username'] ?? null) ? $data['username'] : $uuidStr;
					/** @var array<int,array<string,mixed>> $nodes */
					$nodes = is_array($data['nodes'] ?? null) ? $data['nodes'] : [];
					foreach($nodes as $rawNode){
						$key = is_string($rawNode['key'] ?? null) ? $rawNode['key'] : '';
						if($this->matchesQuery($key, $query)){
							/** @var array<string,string> $ctx */
							$ctx = is_array($rawNode['context'] ?? null) ? $rawNode['context'] : [];
							$expiry = is_int($rawNode['expiry'] ?? null) ? $rawNode['expiry'] : null;
							$results[] = [
								'type' => 'user',
								'name' => $username,
								'node' => new NodeEntry($key, (bool) ($rawNode['value'] ?? true), $ctx, $expiry),
							];
						}
					}
				}catch(\Throwable){}
			}
		}

		if(count($results) === 0){
			$sender->sendMessage(TF::YELLOW . "No matches found for '$query'.");
			return;
		}

		$perPage = 15;
		$total = count($results);
		$pages = (int) ceil($total / $perPage);
		$page = max(1, min($page, $pages));

		$sender->sendMessage(TF::GOLD . "--- Search results for '" . TF::WHITE . $query . TF::GOLD . "' [$page/$pages] (total: $total) ---");
		foreach(array_slice($results, ($page - 1) * $perPage, $perPage) as $r){
			/** @var NodeEntry $node */
			$node = $r['node'];
			$colour = $node->getValue() ? TF::GREEN : TF::RED;
			$temp = $node->isTemporary() ? TF::GRAY . ' (temp)' : '';
			$sender->sendMessage(
				TF::AQUA . '[' . $r['type'] . '] ' .
				TF::WHITE . $r['name'] . ' ' .
				$colour . $node->getKey() .
				$temp
			);
		}
		if($page < $pages){
			$sender->sendMessage(TF::GRAY . 'Use /' . $aliasUsed . ' search ' . $query . ' ' . ($page + 1) . ' to see more.');
		}
	}

	private function matchesQuery(string $key, string $query) : bool{
		$key = strtolower($key);
		$q = strtolower($query);
		// wildcard pattern (e.g. luckperms.*)
		if(str_contains($q, '*') || str_contains($q, '?')){
			return fnmatch($q, $key);
		}
		return str_contains($key, $q);
	}
}
