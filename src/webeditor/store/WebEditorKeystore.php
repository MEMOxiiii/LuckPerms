<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\store;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function json_decode;
use function json_encode;
use function mkdir;
use function dirname;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

class WebEditorKeystore{
	/** @var array<string, array<string, string>> */
	private array $entries = [];

	public function __construct(private string $path){
		$this->load();
	}

	public function get(string $id) : ?array{
		return $this->entries[$id] ?? null;
	}

	public function set(string $id, array $value) : void{
		$this->entries[$id] = $value;
		$this->save();
	}

	private function load() : void{
		if(!file_exists($this->path)){
			$this->entries = [];
			return;
		}

		$contents = file_get_contents($this->path);
		if($contents === false || $contents === ''){
			$this->entries = [];
			return;
		}

		try{
			$data = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
			$this->entries = is_array($data) ? $data : [];
		}catch(\JsonException){
			$this->entries = [];
		}
	}

	private function save() : void{
		$dir = dirname($this->path);
		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}

		file_put_contents($this->path, json_encode($this->entries, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
	}

}
