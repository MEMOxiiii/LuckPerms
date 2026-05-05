<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor;

class WebEditorResponse{
	/** @param array<int, array<string, mixed>> $permissionHolders */
	/** @param array<int, array<string, mixed>> $tracks */
	/** @param array<int, string> $userDeletions */
	/** @param array<int, string> $groupDeletions */
	/** @param array<int, string> $trackDeletions */
	public function __construct(
		private array $permissionHolders,
		private array $tracks,
		private array $userDeletions,
		private array $groupDeletions,
		private array $trackDeletions
	){ }

	/** @return array<int, array<string, mixed>> */
	public function permissionHolders() : array{
		return $this->permissionHolders;
	}

	/** @return array<int, array<string, mixed>> */
	public function tracks() : array{
		return $this->tracks;
	}

	/** @return array<int, string> */
	public function userDeletions() : array{
		return $this->userDeletions;
	}

	/** @return array<int, string> */
	public function groupDeletions() : array{
		return $this->groupDeletions;
	}

	/** @return array<int, string> */
	public function trackDeletions() : array{
		return $this->trackDeletions;
	}

	/**
	 * Build a normalized response from web editor payloads.
	 * Supports both legacy changes-based payload and official permissionHolders format.
	 *
	 * @param array<string, mixed> $payload
	 */
	public static function fromArray(array $payload) : self{
		$holders = [];
		if(isset($payload['permissionHolders']) && is_array($payload['permissionHolders'])){
			$holders = $payload['permissionHolders'];
		}elseif(isset($payload['changes']) && is_array($payload['changes'])){
			$holders = $payload['changes'];
		}

		$tracks = isset($payload['tracks']) && is_array($payload['tracks']) ? $payload['tracks'] : [];
		$userDeletions = isset($payload['userDeletions']) && is_array($payload['userDeletions']) ? array_values(array_map('strval', $payload['userDeletions'])) : [];
		$groupDeletions = isset($payload['groupDeletions']) && is_array($payload['groupDeletions']) ? array_values(array_map('strval', $payload['groupDeletions'])) : [];
		$trackDeletions = isset($payload['trackDeletions']) && is_array($payload['trackDeletions']) ? array_values(array_map('strval', $payload['trackDeletions'])) : [];

		return new self($holders, $tracks, $userDeletions, $groupDeletions, $trackDeletions);
	}

}
