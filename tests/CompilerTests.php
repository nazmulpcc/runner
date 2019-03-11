<?php
require __DIR__ .'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use nazmulpcc\Compilers\CCompiler;
use nazmulpcc\Compilers\CppCompiler;
use nazmulpcc\Compilers\JavaCompiler;
use nazmulpcc\Compilers\PhpCompiler;

/**
 * Test The compilers
 */
class CompilerTests extends TestCase
{
	public function test_c_compiler()
	{
		$code = __DIR__. '/../codes/code.c';
		$compiler = new CCompiler($code);
		$this->assertInstanceOf('nazmulpcc\Compilers\CCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertEquals($compiler->getOutput(), "");
		$compiler->cleanUp();
	}

	public function test_cpp_compiler()
	{
		$code = __DIR__. '/../codes/code.cpp';
		$compiler = new CppCompiler($code);
		$compiler->setVersion(17);
		$this->assertInstanceOf('nazmulpcc\Compilers\CppCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertEquals($compiler->getOutput(), "");
		$compiler->cleanUp();
	}

	public function test_java_compiler()
	{
		$code = __DIR__. '/../codes/code.java';
		$compiler = new JavaCompiler($code);
		$this->assertInstanceOf('nazmulpcc\Compilers\JavaCompiler', $compiler);
		$this->assertTrue($compiler->compile());
		$this->assertEquals($compiler->getOutput(), "");
		$compiler->cleanUp();
	}

	// public function test_php_compiler()
	// {
	// 	$code = __DIR__. '/../codes/code.php';
	// 	$compiler = new PhpCompiler($code, $code);
	// 	$this->assertInstanceOf('nazmulpcc\Compilers\PhpCompiler', $compiler);
	// 	$this->assertTrue($compiler->compile());
	// 	$this->assertContains("No syntax errors detected", $compiler->getOutput());
	//  $compiler->cleanUp();
	// }
}