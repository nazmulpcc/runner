<?php

namespace nazmulpcc\Compilers;

use Exception;
use nazmulpcc\Traits\Events;
use nazmulpcc\Traits\Isolator;
use nazmulpcc\Traits\BootTraits;
/**
 * Base Compiling class
 */
abstract class BaseCompiler
{
	use Events, Isolator, BootTraits;
	/**
	 * The compiler command/path to execute
	 * @var string
	 */
	protected $compiler;
	/**
	 * Location of the code to be compiled
	 * @var string
	 */
	protected $codePath;
	/**
	 * Where the compiled file should go
	 * @var string
	 */
	protected $objectPath = false;
	/**
	 * The last command exit status
	 * @var int
	 */
	protected $exitStatus;
	/**
	 * Last Command Output
	 * @var string
	 */
	protected $output = "";
	/**
	 * Where the input should be received
	 * @var boolean
	 */
	protected $inputFilePath = false, $outputFilePath = false, $errorFilePath = false;
	/**
	 * Time limit for execution
	 * @var integer
	 */
	protected $timeLimit = 1;
	/**
	 * File output limit
	 *
	 * @var string
	 */
	protected $fileSize = false;
	/**
	 * Memory limits in KB for runing
	 * @var [type]
	 */
	protected $memoryLimit = 512 * 1024;
	/**
	 * Location to save meta data
	 *
	 * @var string
	 */
	protected $metaFile = false;
	/**
	 * Last command that was executed
	 * @var [type]
	 */
	protected $lastExecuted;
	/**
	 * Should everything be cleaned after run
	 * @var boolean
	 */
	protected $autoClean = true;

	function __construct($code, $object = false)
	{
		if (!$object) {
			$object = dirname($code) . "/object";
		}
		$this->setCodePath($code)->setObjectPath($object);
		$this->bootTraits(); // thank you laravel
		$this->fireEvents('booted');
	}

	/**
	 * @param string $code Location of the code to be compiled
	 */
	public function setCodePath($codePath)
	{
		if(!file_exists($codePath)){
			throw new Exception("File not found {$codePath}");
		}
		$this->codePath = $codePath;
		return $this;
	}

	/**
	 * Set where the compiled file should go
	 * @param string $object The object location
	 */
	public function setObjectPath($objectPath)
	{
		$this->checkFile(dirname($objectPath), true);
		$this->objectPath = $objectPath;
		return $this;
	}

	/**
	 * Get the last command exit status
	 * @return int Get the last command exit status
	 */
	public function getExitStatus()
	{
		return $this->exitStatus;
	}

	/**
	 * Execute a command, update exit status and output
	 *
	 * @param string $cmd
	 * @return static
	 */
	public function exec($cmd)
	{
		$this->lastExecuted = $cmd;
		exec($cmd, $output, $this->exitStatus);
		$this->updateOutput($output);
		return $this;
	}
	
	/**
	 * Compile the code
	 * @return bool
	 */
	public function compile()
	{
		$this->fireEvents('beforeCompiling');
		$this->exec($this->getCompileCommand());
		$this->fireEvents('afterCompiled');
		return $this->checkSuccess();
	}

	/**
	 * Select the input file
	 * @param  string $file The input file location
	 * @return static       This
	 */
	public function input($file)
	{
		$this->checkFile($file);
		$this->inputFilePath = $file;
		return $this;
	}

	/**
	 * Seletc the output file location
	 * @param  string $file the output file
	 * @return static       this
	 */
	public function output($file)
	{
		$this->checkFile(dirname($file));
		$this->outputFilePath = $file;
		return $this;
	}

	/**
	 * Seletc the output file location
	 * @param  string $file the output file
	 * @return static       this
	 */
	public function error($file)
	{
		$this->checkFile(dirname($file));
		$this->errorFilePath = $file;
		return $this;
	}

	/**
	 * Set the time limit
	 * @param  integer $seconds The time limit
	 * @return static
	 */
	public function time($seconds = 1)
	{
		$this->timeLimit = $seconds;
		return $this;
	}

	/**
	 * Set the memory limit
	 * @param  integer $mb The memory limit
	 * @return static
	 */
	public function memory($mb = 1024)
	{
		$this->memoryLimit = $mb * 1024;
		return $this;
	}

	/**
	 * Limit output file Size
	 *
	 * @param integer $mb
	 * @return void
	 */
	public function fileSize($mb = 100)
	{
		$this->fileSize = $mb * 1024;
		return $this;
	}

	/**
	 * File to save meta information if any
	 *
	 * @param string $file
	 * @return static
	 */
	public function meta($file)
	{
		$this->checkFile(dirname($file));
		$this->metaFile = $file;
		return $this;
	}

	/**
	 * Launch the compiled file.
	 * @return bool If the run was successful
	 */
	public function run()
	{
		$this->fireEvents('beforeRun');
		$this->exec($this->buildRunCommandWithIO());
		$this->fireEvents('afterRun');
		// $this->inputFilePath = $this->outputFilePath = false;
		return $this->checkSuccess();
	}

	/**
	 * Get the last output
	 * @return The last output [description]
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * Get the last executed command
	 * @return string
	 */
	public function last()
	{
		return $this->lastExecuted;
	}

	/**
	 * Build The command to run the object file
	 * @return string          The command that should run
	 */
	protected function buildRunCommandWithIO()
	{
		$error = $this->errorFilePath ? $this->errorFilePath : "/dev/null";
		$cmd = $this->inputFilePath ? "cat {$this->inputFilePath} | " : "";
		$cmd .= $this->getRunCommand();
		$cmd .= $this->outputFilePath ? " > {$this->outputFilePath}" : "";
		$cmd .= " 2>$error";
		return $cmd;
	}

	/**
	 * Update the command output
	 * @param  string|array $output The output of a command
	 * @return static
	 */
	protected function updateOutput($output)
	{
		if(is_array($output)){
			$this->output = implode("\n", $output);
		}else{
			$this->output = (string) $output;
		}
		return $this;
	}

	/**
	 * Check the compiler output to detect if the compilation was successfull
	 * @return bool
	 */
	protected function checkSuccess()
	{
		return !$this->exitStatus;
	}

	/**
	 * Check if a file exists and optionally writable
	 * @param  string  $path     The file location that should be checked
	 * @param  boolean $writable If the file should be writable
	 * @return boolean
	 */
	protected function checkFile($path, $writable = false)
	{
		if ($path && !file_exists($path)) {
			throw new Exception("{$path} doesn't exist");
		}
		if ($path && $writable && !is_writable($path)) {
			throw new Exception("{$path} is not writable");
		}
		return (bool) $path;
	}

	protected function copyFile($from, $to)
	{
		if(!file_exists(dirname($to))){
			@mkdir(dirname($to), '0777', true);
		}
		$this->checkFile($from);
		$this->checkFile($to, true);
		exec("cp $from $to >/dev/null 2>/dev/null", $output, $exit);
		return $exit == 0;
	}

	public function cleanUpAfterRun()
	{
		if($this->autoClean){
			$this->cleanUp();
		}
	}

	/**
	 * Get the command to compile the code.
	 * @return [type] [description]
	 */
	public abstract function getCompileCommand();

	public function cleanUp()
	{
		// @unlink($this->outputFilePath);
		@unlink($this->objectPath);
		return;
	}

	public function __destruct()
	{
		$this->fireEvents('destroying');
	}
}