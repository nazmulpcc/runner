<?php

namespace nazmulpcc\Compilers;

use nazmulpcc\Traits\HasVersions;

/**
 * Java compiler class
 */
class JavaCompiler extends BaseCompiler
{
	use HasVersions;

	public function __construct($code, $object = false)
	{
		$object = $object ? : dirname($code). "/Main.class";
		parent::__construct($code, $object);
		$this->memory(4096); // set memory for java vm
	}

	public function getCompileCommand()
	{
		return "{$this->getVersionedCompiler('compiler')} {$this->codePath} 2>&1";
	}

	public function getRunCommand()
	{
		return $this->isolate("-p --run {$this->getVersionedCompiler('runner')} Main");
	}

	public static function getCompilerVersions()
	{
		return [
			'java8' => [
				'compiler' => '/usr/lib/jvm/java-8-openjdk-amd64/bin/javac',
				'runner'   => '/usr/lib/jvm/java-8-openjdk-amd64/jre/bin/java'
			]
		];
	}

	protected function getDefaultCompiler()
	{
		return 'java8';
	}	
}