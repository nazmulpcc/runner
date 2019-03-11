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

	public function getCompileCommand()
	{
		return "{$this->getVersionedCompiler()} -l {$this->codePath}";
	}

	public function getRunCommand()
	{
		return $this->isolate("/usr/bin/php /box/". basename($this->objectPath));
	}

	public function getDefaultCompiler()
	{
		return '/usr/bin/php';
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
			'7.2' => '/usr/bin/php'
		];
	}

	public function cleanUp()
	{
		@unlink($this->outputFilePath);
	}
	
}