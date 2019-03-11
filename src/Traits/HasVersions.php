<?php

namespace nazmulpcc\Traits;

trait HasVersions {

	protected static $versions = [];

	protected static $compilers = [];

	protected $version;

	public function setVersion($version)
	{
		$this->version = $version;
		return $this;
	}

	public function getVersionedCompiler()
	{
		$compilers = static::getCompilers();
		if (isset($compilers[$this->version])) {
			return $compilers[$this->version];
		}else{
			return $this->getDefaultCompiler();
		}
	}

	/**
	 * Correct way to get list of available compilers
	 * @return array
	 */
	public static function getCompilers()
	{
		return array_merge(static::getCompilerVersions(), static::$compilers);
	}

	/**
	 * Override when using this trait
	 */
	protected static function getCompilerVersions()
	{
		return [];
	}

	/**
	 * Set compiler for a specific version
	 * @param string $version The version
	 * @param string $path    The path/command
	 */
	public static function setCompiler(string $version, string $path)
	{
		if(!in_array($version, static::getCompilers())){
			static::$compilers[$version] = $path;
		}
	}

	/**
	 * Set compilers
	 * @param array $compilers A map of version=>path for compilers
	 */
	public static function setCompilers(array $compilers)
	{
		foreach ($compilers as $version => $path) {
			static::setCompiler($version, $path);
		}
	}
}