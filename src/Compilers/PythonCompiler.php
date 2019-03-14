<?php

namespace nazmulpcc\Compilers;

use nazmulpcc\Traits\Isolator;
use nazmulpcc\Traits\HasVersions;

/**
 * PHP compiler class
 */
class PythonCompiler extends BaseCompiler
{
	protected $autoClean = true;

	use HasVersions, Isolator;

	public function __construct($code)
	{
		$object = $code;
		parent::__construct($code, $object);
	}

	public function compile()
	{
		return true;
	}

	public function getCompileCommand()
	{
		return null;
	}

	public function getRunCommand()
	{
		return $this->isolate("--run {$this->getVersionedCompiler('runner')} /box/". basename($this->objectPath));
	}

	public function getDefaultCompiler()
	{
		return '3.5';
	}

	/**
	 * Call getCompilers to get the list of available compilers
	 * @return array
	 */
	protected static function getCompilerVersions()
	{
		return [
			'2.7' => '/usr/bin/python2.7',
			'3.5' => '/usr/bin/python3.5'
		];
	}

	public function cleanUp()
	{
		@unlink($this->outputFilePath);
	}
	
}