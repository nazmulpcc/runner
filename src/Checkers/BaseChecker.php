<?php

namespace nazmulpcc\Checkers;

abstract class BaseChecker
{
    /**
     * The main answer file
     *
     * @var string
     */
    protected $sourceFile = false;
    /**
     * The participants output file
     *
     * @var string
     */
    protected $outputFile = false;
    /**
     * The log file
     *
     * @var string
     */
    protected $logFile = false;
    /**
     * Should the Checker check for presenation error
     *
     * @var boolean
     */
    protected static $checkPresenation = false;
    /**
     * The folder where temporary data will be stored
     *
     * @var string
     */
    protected static $tmpDir = "/tmp";

    /**
     * Set if the checker should check for presentation error
     *
     * @param bool $check
     * @return void
     */
    public static function presentation($check)
    {
        static::$checkPresenation = $check;
    }
    /**
     * Set the temporary data location
     *
     * @param string $dir
     * @return void
     */
    public static function tmpDir($dir)
    {
        if(!file_exists($dir) && !is_writable($dir)){
            throw new Exception("$dir is not writable");
        }
        static::$tmpDir = $dir;
    }
    /**
     * Set the output file
     *
     * @param string $file
     * @return static
     */
    public function output($file)
    {
        $this->fileMustExist($file);
        $this->outputFile = $file;
        return $this;
    }
    
    /**
     * The file against which output will be checked
     *
     * @param string $file
     * @return static
     */
    public function source($file)
    {
        $this->fileMustExist($file);
        $this->sourceFile = $file;
        return $this;
    }

    public function log($file)
    {
        if(!file_exists(dirname($file)) && !is_writable(dirname($file))){
            throw new Exception("$file is not writable");
        }
        $this->logFile = $file;
        return $this;
    }

    /**
     * Check if the file exists
     *
     * @param string $file
     * @return static
     */
    public function fileMustExist($file)
    {
        if(!file_exists($file)){
            throw new Exception("{$file} doesn't exist.");
        }
        return $this;
    }

    public function diff($source, $output)
    {
        exec("diff -q {$source} {$output}", $out, $exit);
        return $exit ? false : true;
    }
    /**
     * Remove all whitespace from $source and write the file to $destination
     *
     * @param string $source
     * @param string $destination
     * @return bool|string
     */
    public function removeWhiteSpaces($source, $destination)
    {
        if(!file_exists($source) || !file_exists(dirname($destination))){
            throw new Exception("Cannot remove whitespace because file/folder not available.");
        }
        exec("cat {$source} | tr -d \"[:space:]\" > {$destination} 2>/dev/null", $out, $exit);
        return $exit ? false : $destination;
    }

    /**
     * Delete a file
     *
     * @param string $file
     * @return void
     */
    public function delete($file)
    {
        if(is_writable($file)){
            return unlink($file);
        }
        return false;
    }

    /**
     * Get the temporary path for a file
     *
     * @param string $file
     * @return string
     */
    public function tmpPath($file = false)
    {
        $file = $file ? : rand(100000000, 999999999);
        return static::$tmpDir . '/' . trim($file, " /\t\n\r\0");
    }

    public abstract function check();
    public function checkPresentation(){
        return true;
    }
}
