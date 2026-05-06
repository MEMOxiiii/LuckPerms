<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\storage;

use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Group;
use jasonw4331\LuckPerms\model\Track;
use jasonw4331\LuckPerms\model\User;
use jasonw4331\LuckPerms\node\NodeEntry;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function array_map;
use function array_values;
use function basename;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function json_decode;
use function json_encode;
use function mkdir;
use function scandir;
use function str_ends_with;
use function strtolower;
use function unlink;
use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

class Storage{
	/** @var array<string, string> */
	private array $uuidToName = [];
	/** @var array<string, string> */
	private array $nameToUuid = [];

	public function __construct(private LuckPerms $plugin){ }

	private function getUserDir() : string{
		$dir = $this->plugin->getDataFolder() . 'users' . DIRECTORY_SEPARATOR;
		if(!is_dir($dir)) @mkdir($dir, 0777, true);
		return $dir;
	}

	private function getGroupDir() : string{
		$dir = $this->plugin->getDataFolder() . 'groups' . DIRECTORY_SEPARATOR;
		if(!is_dir($dir)) @mkdir($dir, 0777, true);
		return $dir;
	}

	/** @param NodeEntry[] $nodes */
	private function serializeNodes(array $nodes) : array{
		return array_values(array_map(static fn(NodeEntry $n) => [
			'key'     => $n->getKey(),
			'value'   => $n->getValue(),
			'context' => $n->getContext(),
			'expiry'  => $n->getExpiry(),
		], $nodes));
	}

	/** @return NodeEntry[] */
	private function deserializeNodes(array $rawNodes) : array{
		$nodes = [];
		foreach($rawNodes as $nodeData){
			$node = NodeEntry::fromArray($nodeData);
			if($node !== null) $nodes[] = $node;
		}
		return $nodes;
	}

	/* ─────────────── Users ─────────────── */

	public function saveUser(User $user) : void{
		$uuid = $user->getUniqueId()->toString();
		$name = strtolower($user->getUsername());
		$this->uuidToName[$uuid] = $user->getUsername();
		$this->nameToUuid[$name] = $uuid;
		$data = [
			'uniqueId' => $uuid,
			'username' => $user->getUsername(),
			'nodes'    => $this->serializeNodes($user->getNodes()),
		];
		file_put_contents(
			$this->getUserDir() . $uuid . '.json',
			json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
		);
	}

	public function loadUser(UuidInterface $uniqueId) : ?User{
		$file = $this->getUserDir() . $uniqueId->toString() . '.json';
		if(!file_exists($file)) return null;
		try{
			$data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
		}catch(\Throwable){
			return null;
		}
		$user = new User($uniqueId, $data['username'] ?? 'Unknown');
		$user->setNodes($this->deserializeNodes($data['nodes'] ?? []));
		$this->uuidToName[$uniqueId->toString()] = $data['username'] ?? 'Unknown';
		$this->nameToUuid[strtolower($data['username'] ?? 'Unknown')] = $uniqueId->toString();
		return $user;
	}

	/* ─────────────── Groups ─────────────── */

	public function saveGroup(Group $group) : void{
		$data = [
			'name'        => $group->getName(),
			'weight'      => $group->getWeight(),
			'displayName' => $group->getDisplayName(),
			'nodes'       => $this->serializeNodes($group->getNodes()),
		];
		file_put_contents(
			$this->getGroupDir() . strtolower($group->getName()) . '.json',
			json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
		);
		// Ensure the group is in the manager with up-to-date data
		$managed = $this->plugin->getGroupManager()->getOrMake($group->getName());
		$managed->setNodes($group->getNodes());
		$managed->setWeight($group->getWeight());
		$managed->setDisplayName($group->getDisplayName());
	}

	public function loadGroup(string $name) : ?Group{
		$file = $this->getGroupDir() . strtolower($name) . '.json';
		if(!file_exists($file)) return null;
		try{
			$data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
		}catch(\Throwable){
			return null;
		}
		$group = new Group($data['name'] ?? $name);
		$group->setNodes($this->deserializeNodes($data['nodes'] ?? []));
		$group->setWeight((int) ($data['weight'] ?? 0));
		$group->setDisplayName($data['displayName'] ?? null);
		return $group;
	}

	public function loadAllGroups() : void{
		$dir = $this->getGroupDir();
		foreach(scandir($dir) ?: [] as $file){
			if(!str_ends_with($file, '.json')) continue;
			$name = basename($file, '.json');
			$group = $this->loadGroup($name);
			if($group !== null){
				$loaded = $this->plugin->getGroupManager()->getOrMake($group->getName());
				$loaded->setNodes($group->getNodes());
			}
		}
	}

	public function deleteGroup(string $name) : void{
		$file = $this->getGroupDir() . strtolower($name) . '.json';
		if(file_exists($file)) @unlink($file);
		$this->plugin->getGroupManager()->delete($name);
	}

	/* ─────────────── Tracks ─────────────── */

	private function getTracksFile() : string{
		return $this->plugin->getDataFolder() . 'tracks.json';
	}

	/** @return array<string, array{name: string, groups: string[]}> */
	private function loadTracksData() : array{
		$file = $this->getTracksFile();
		if(!file_exists($file)) return [];
		try{
			return json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR) ?? [];
		}catch(\Throwable){
			return [];
		}
	}

	public function saveTrack(Track $track) : void{
		$allTracks = $this->loadTracksData();
		$allTracks[strtolower($track->getName())] = [
			'name'   => $track->getName(),
			'groups' => $track->getGroups(),
		];
		file_put_contents(
			$this->getTracksFile(),
			json_encode($allTracks, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
		);
		// Ensure the track is in the manager
		$managed = $this->plugin->getTrackManager()->getOrMake($track->getName());
		$managed->setGroups($track->getGroups());
	}

	public function loadAllTracks() : void{
		foreach($this->loadTracksData() as $data){
			$track = $this->plugin->getTrackManager()->getOrMake($data['name'] ?? '');
			$track->setGroups($data['groups'] ?? []);
		}
	}

	public function deleteTrack(string $name) : void{
		$allTracks = $this->loadTracksData();
		unset($allTracks[strtolower($name)]);
		file_put_contents(
			$this->getTracksFile(),
			json_encode($allTracks, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
		);
		$this->plugin->getTrackManager()->delete($name);
	}

	/* ─────────────── UUID / Name cache ─────────────── */

	public function getPlayerUniqueId(string $username) : ?UuidInterface{
		$uuid = $this->nameToUuid[strtolower($username)] ?? null;
		return $uuid !== null ? Uuid::fromString($uuid) : null;
	}

	public function getPlayerName(UuidInterface|string $uniqueId) : ?string{
		$key = $uniqueId instanceof UuidInterface ? $uniqueId->toString() : $uniqueId;
		return $this->uuidToName[$key] ?? null;
	}

	public function shutdown() : void{
		// All data is saved immediately on change — nothing to flush
	}
}

