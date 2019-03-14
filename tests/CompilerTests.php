<?php
require __DIR__ .'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use nazmulpcc\Compilers\CCompiler;
use nazmulpcc\Compilers\CppCompiler;
use nazmulpcc\Compilers\JavaCompiler;
use nazmulpcc\Compilers\PhpCompiler;
use nazmulpcc\Compilers\PythonCompiler;
use nazmulpcc\Checkers\Verdict;
use nazmulpcc\Checkers\Standard as Checker;

/**
 * Test The compilers
 */
class CompilerTests extends TestCase
{
	public function test_c_compiler()
	{
		$code = $this->codePath('code.c');
		$compiler = new CCompiler($code);
		$this->assertInstanceOf('nazmulpcc\Compilers\CCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertEquals($compiler->getOutput(), "");
		$this->assertEquals($this->standardChecker($compiler), Verdict::ACCEPTED);
		$compiler->cleanUp();
	}

	public function test_cpp_compiler()
	{
		$code = $this->codePath('code.cpp');
		$compiler = new CppCompiler($code);
		$compiler->setVersion(17);
		$this->assertInstanceOf('nazmulpcc\Compilers\CppCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertEquals($compiler->getOutput(), "");
		$this->assertEquals($this->standardChecker($compiler), Verdict::ACCEPTED);
		$compiler->cleanUp();
	}

	public function test_java_compiler()
	{
		$code = $this->codePath('code.java');
		$compiler = new JavaCompiler($code);
		$this->assertInstanceOf('nazmulpcc\Compilers\JavaCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertEquals($compiler->getOutput(), "");
		$compiler->memory(4 * 1024);
		$this->assertEquals($this->standardChecker($compiler), Verdict::ACCEPTED);
		$compiler->cleanUp();
	}

	public function test_php_compiler()
	{
		$code = $this->codePath('code.php');
		$compiler = new PhpCompiler($code);
		$this->assertInstanceOf('nazmulpcc\Compilers\PhpCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertContains("No syntax errors detected", $compiler->getOutput()); // TODO: check exit code, not message
		$this->assertEquals($this->standardChecker($compiler), Verdict::ACCEPTED);
		$compiler->cleanUp();
	}

	public function test_python_compiler()
	{
		$code = $this->codePath('code.py');
		$compiler = new PythonCompiler($code);
		$this->assertInstanceOf('nazmulpcc\Compilers\PythonCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertEquals($this->standardChecker($compiler), Verdict::ACCEPTED);
		$compiler->cleanUp();
	}

	/**
	 * Get a standard Checker
	 *
	 * @return Checker
	 */
	public function getChecker()
	{
		return new Checker(['source' => __DIR__. '/../codes/output']);
	}

	public function codePath($path = null)
	{
		return __DIR__. '/../codes/'. trim($path, ' /');
	}

	public function standardChecker($compiler)
	{
		$checker = $this->getChecker();
		return $compiler->output($this->codePath('tmp'))->judge($checker);
	}
}