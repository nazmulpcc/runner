<?php

namespace nazmulpcc\Compilers;

use nazmulpcc\Traits\HasVersions;
use nazmulpcc\Traits\Isolator;

/**
 * CPP compiler class
 */
class CppCompiler extends BaseCompiler
{
	use HasVersions;

	public function getCompileCommand()
	{
		return "/usr/bin/g++ -O2 -pipe -std=c++{$this->getVersionedCompiler('compiler')} -lm {$this->codePath} -o {$this->objectPath} 2>&1";
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
	public static function getCompilerVersions()
	{
		return [
			'11' => ['compiler' => '11'],
			'14' => ['compiler' => '14'],
			'17' => ['compiler' => '17']
		];
	}
}