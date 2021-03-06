<?php

namespace nazmulpcc\Compilers;

use nazmulpcc\Traits\Isolator;
use nazmulpcc\Traits\HasVersions;

/**
 * PHP compiler class
 */
class PhpCompiler extends BaseCompiler
{
	protected $autoClean = true;

	use HasVersions, Isolator;

	public function __construct($code)
	{
		$object = $code;
		parent::__construct($code, $object);
	}

	public function getCompileCommand()
	{
		return "{$this->getVersionedCompiler('compiler')} -l {$this->codePath}";
	}

	public function getRunCommand()
	{
		return $this->isolate("--run {$this->getVersionedCompiler('runner')} /box/". basename($this->objectPath));
	}

	public function getDefaultCompiler()
	{
		return '7.2';
	}

	/**
	 * Call getCompilers to get the list of available compilers
	 * @return array
	 */
	protected static function getCompilerVersions()
	{
		return [
			'5.6' => '/usr/bin/php',
			'7.0' => '/usr/bin/php',
			'7.2' => '/usr/bin/php7.2'
		];
	}

	public function cleanUp()
	{
		@unlink($this->outputFilePath);
	}
	
}