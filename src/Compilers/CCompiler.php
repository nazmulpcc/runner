<?php

namespace nazmulpcc\Compilers;

use nazmulpcc\Traits\HasVersions;

/**
 * C compiler class
 */
class CCompiler extends BaseCompiler
{
	use HasVersions;

	protected $autoClean = true;

	public function getCompileCommand()
	{
		return "/usr/bin/gcc -O2 -pipe -std=c{$this->getVersionedCompiler('compiler')} -lm {$this->codePath} -o {$this->objectPath} 2>&1";
	}

	public function getRunCommand()
	{
		return $this->isolate('--run /box/'. basename($this->objectPath));
	}

	public function getDefaultCompiler()
	{
		return '11';
	}

	/**
	 * Call getCompilers to get the list of available compilers
	 * @return array
	 */
	protected static function getCompilerVersions()
	{
		return [
			'11' => ['compiler' => '11'],
			'14' => ['compiler' => '14'],
			'17' => ['compiler' => '17']
		];
	}
}