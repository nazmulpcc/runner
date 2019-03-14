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

	/**
	 * @param  string Should the path be either compiler's or runner's
	 * @return void
	 */
	public function getVersionedCompiler($type = 'compiler')
	{
		if($type != 'compiler' && $type != 'runner'){
			$type = 'compiler';
		}
		$compilers = static::getCompilers();
		$version = isset($compilers[$this->version]) ? $this->version : $this->getDefaultCompiler();
		return is_string($compilers[$version]) ? $compilers[$version] : $compilers[$version][$type];
	}

	/**
	 * Correct way to get list of available compilers
	 * @return array
	 */
	public static function getCompilers()
	{
		return static::getCompilerVersions() + static::$compilers;
	}

	/**
	 * Override when using this trait
	 */
	public static function getCompilerVersions()
	{
		return [];
	}

	/**
	 * Set compiler for a specific version
	 * @param string $version The version
	 * @param string $path    The path/command
	 */
	public static function setCompiler(string $version, string $compiler, string $runner)
	{
		if(!in_array($version, static::getCompilers())){
			static::$compilers[$version] = compact('compiler', 'runner');
		}
	}

	/**
	 * Set compilers
	 * @param array $compilers A map of version=>[compiler, runner] for compilers
	 */
	public static function setCompilers(array $compilers)
	{
		foreach ($compilers as $version => $path) {
			static::setCompiler($version, $path['compiler'], $path['runner']);
		}
	}
}