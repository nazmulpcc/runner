<?php

namespace nazmulpcc\Traits;

trait CanBeTested {
	protected $inputFiles = [];
	protected $outputFiles = [];

	public function inputs($files)
	{
		foreach ($files as $file) {
			$this->checkFile($file);
		}
		$this->inputFiles = $files;
		return $this;
	}

	public function outputs($files)
	{
		foreach ($files as $file) {
			$this->checkFile(dirname($file), true);
		}
		if(count($this->inputFiles) != $this->outputFiles){
			throw new Exception("Input and output file count should be same");
		}
		$this->outputFiles = $files;
		return $this;
	}

	public function test()
	{
		for ($i=0; $i < count($this->inputFiles); $i++) { 
			$this->input($this->inputFiles[$i])->output($this->outputFiles[$i])->run();
		}
	}
}