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
		return "{$this->getVersionedCompiler()} {$this->codePath} 2>&1";
	}

	public function getRunCommand()
	{
		return $this->isolate("-p --run /usr/lib/jvm/java-8-openjdk-amd64/jre/bin/java Main");
	}

	protected function getDefaultCompiler()
	{
		return '/usr/lib/jvm/java-8-openjdk-amd64/bin/javac';
	}	
}