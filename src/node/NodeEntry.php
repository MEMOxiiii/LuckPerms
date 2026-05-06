<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\node;

use function is_array;
use function is_bool;
use function is_int;
use function is_string;

/**
 * Lightweight representation of a single permission node as stored/transmitted
 * (e.g. from the web editor or storage layer).
 */
class NodeEntry{
		/**
		 * @param array<string, string> $context key→value pairs
		 */
		public function __construct(
				private string $key,
				private bool $value,
				private array $context,
				private ?int $expiry
		){ }

		public function getKey() : string{
				return $this->key;
		}

		public function getValue() : bool{
				return $this->value;
		}

		/** @return array<string, string> */
		public function getContext() : array{
				return $this->context;
		}

		public function getExpiry() : ?int{
				return $this->expiry;
		}

		public function isTemporary() : bool{
				return $this->expiry !== null && $this->expiry > 0;
		}

		/**
		 * Deserialize a node from a web-editor / storage array entry.
		 *
		 * Expected shape:
		 *   { "key": "some.permission", "value": true, "context": {"server":"survival"}, "expiry": null }
		 *
		 * @param array<string, mixed> $data
		 */
		public static function fromArray(array $data) : ?self{
				if(!isset($data['key']) || !is_string($data['key']) || $data['key'] === ''){
						return null;
				}
				$key = $data['key'];
				$value = isset($data['value']) && is_bool($data['value']) ? $data['value'] : true;

				$context = [];
				if(isset($data['context']) && is_array($data['context'])){
						foreach($data['context'] as $k => $v){
								if(is_string($k) && is_string($v)){
										$context[$k] = $v;
								}
						}
				}

				$expiry = null;
				if(isset($data['expiry']) && is_int($data['expiry']) && $data['expiry'] > 0){
						$expiry = $data['expiry'];
				}

				return new self($key, $value, $context, $expiry);
		}

		/** @return array<string, mixed> */
		public function toArray() : array{
				return [
					'key' => $this->key,
					'value' => $this->value,
					'context' => $this->context,
					'expiry' => $this->expiry,
				];
		}
}
