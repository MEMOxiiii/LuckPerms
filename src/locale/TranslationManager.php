<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\locale;

use jasonw4331\LuckPerms\LuckPerms;
use function basename;
use function file_exists;
use function is_dir;
use function mkdir;
use function str_ends_with;
use function strlen;
use function substr;
use const DIRECTORY_SEPARATOR;

/**
 * @template T
 */
final class TranslationManager{
	/** The default locale used by LuckPerms messages */
	public const DEFAULT_LOCALE = 'en_US';

	/** @var array<string> $installed locale strings */
	private array $installed = [];
	private TranslationRegistry $registry;

	private string $translationsDirectory;
	private string $repositoryTranslationsDirectory;
	private string $customTranslationsDirectory;

	public function __construct(private LuckPerms $plugin){
		$dataFolder = $plugin->getDataFolder();
		$this->translationsDirectory = $dataFolder . "translations" . DIRECTORY_SEPARATOR;
		$this->repositoryTranslationsDirectory = $this->translationsDirectory . "repository" . DIRECTORY_SEPARATOR;
		$this->customTranslationsDirectory = $this->translationsDirectory . "custom" . DIRECTORY_SEPARATOR;

		if(!is_dir($this->repositoryTranslationsDirectory)){
			@mkdir($this->repositoryTranslationsDirectory);
		}
		if(!is_dir($this->customTranslationsDirectory)){
			@mkdir($this->customTranslationsDirectory);
		}
	}

	public function getTranslationsDirectory() : string{
		return $this->translationsDirectory;
	}

	public function getRepositoryTranslationsDirectory() : string{
		return $this->repositoryTranslationsDirectory;
	}

	public function getRepositoryStatusFile() : string{
				return $this->repositoryTranslationsDirectory . "status.json";
	}

	public function getInstalledLocales() : array{
		return $this->installed;
	}

	public function reload() : void{
		$this->installed = [];
		// Load built-in translations from properties file
		$this->loadBuiltinTranslations();
		// Load custom/repository translations from filesystem
		if(is_dir($this->customTranslationsDirectory)){
			$this->loadFromFileSystem($this->customTranslationsDirectory);
		}
		if(is_dir($this->repositoryTranslationsDirectory)){
			$this->loadFromFileSystem($this->repositoryTranslationsDirectory);
		}
	}

	private function loadBuiltinTranslations() : void{
		$file = $this->plugin->getResourcePath('luckperms_en.properties');
		if(file_exists($file)){
			$this->loadTranslationFile($file);
		}
	}

	public function isTranslationFile(string $path) : bool{
		return str_ends_with($path, ".properties");
	}

	public function loadFromFileSystem(string $directory) : void{
		/** @var \SplFileInfo $file */
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)) as $file){
			if($file->isFile() && $this->isTranslationFile($file->getFilename())){
				$this->loadTranslationFile($file->getPathname());
			}
		}
	}

	private function loadTranslationFile(string $path) : void{
		$fileName = basename($path);
		$localeString = substr($fileName, 0, -strlen(".properties"));
		$this->installed[] = $localeString;
	}

}
