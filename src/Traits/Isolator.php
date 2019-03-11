<?php

namespace nazmulpcc\Traits;

/**
 * Isolator Trait
 */
trait Isolator
{    
    /**
     * The isolate command
     *
     * @var string
     */
    protected $isolate = false;

    /**
     * The root box path
     *
     * @var string
     */
    protected $boxPath;
    /**
     * The meta values
     *
     * @var array
     */
    protected $metas = [];
    
    /**
     * Boot up the trait
     *
     * @return void
     */
    public function bootIsolator()
    {
        $this->registerEvent('beforeRun', 'initializeIsolator');
        $this->registerEvent('afterRun', 'processMetaFile');
        $this->registerEvent('afterRun', 'cleanUpBox');
        $this->registerEvent('destroying', 'cleanUpBox');
    }

    /**
     * Find an empty id and initialize Isolate
     *
     * @return static
     */
    public function initializeIsolator()
    {
        if(!$this->isInitialized()){
            $this->isolate = "isolate --box-id={$this->findEmptyBox()}";
            exec("{$this->isolate} --init", $output, $exit);
            $this->boxPath = $output[0];
        }
        $this->copyObjectToBox();
        return $this;
    }

    /**
     * If object file exists, copy it inside the box so we can execute it.
     *
     * @return static
     */
    public function copyObjectToBox()
    {
        if($this->objectPath && file_exists($this->objectPath)){
            $this->copyFile($this->objectPath, "{$this->boxPath}/box/");
        }
        return $this;
    }

    public function isolate($cmd, $exec = false)
    {
        $cmd = "{$this->buildIsolateCommand()} $cmd";
        if($exec){
            return $this->exec($cmd);
        }
        return $cmd;
    }

    public function buildIsolateCommand()
    {
        $cmd = $this->isolate;
        !$this->memoryLimit ? : $cmd .= " -m {$this->memoryLimit}";
        !$this->timeLimit   ? : $cmd .= " -t {$this->timeLimit} -w {$this->getWallTime()}";
        !$this->fileSize    ? : $cmd .= " -f {$this->fileSize}";
        !$this->metaFile    ? : $cmd .= " -M {$this->metaFile}";
        return $cmd;
    }

    /**
     * Get the wall time
     *
     * @return integer
     */
    protected function getWallTime()
    {
        return $this->timeLimit + 2;
    }

    /**
     * Check if the islator has been initialized
     *
     * @return void
     */
    public function isInitialized()
    {
        return (bool) $this->isolate;
    }

    /**
     * Process a meta file generated by Isolator
     *
     * @param string $file
     * @return static
     */
    public function processMetaFile($file = false)
    {
        $file = $file ? : $this->metaFile;
        if(!$file || ! file_exists($file)){
            return false;
        }
        $contents = explode("\n", file_get_contents($file));
        $metas = [];
        foreach ($contents as $keys){
            if(strpos($keys, ":") != false) {
                list($key, $val) = explode(":", $keys);
                $metas[$key] = $val;
            }
        }
        return $metas;
    }
    /**
     * Get a meta value by a key
     *
     * @param string $key
     * @return mixed
     */
    public function getMeta($key = false)
    {
        if(!$key){
            return $this->metas;
        }elseif(isset($this->metas[$key])){
            return $this->metas[$key];
        }else{
            return null;
        }
    }
    /**
     * Clean up the isolator box
     *
     * @return bool
     */
    public function cleanUpBox()
    {
        if($this->isolate){
            exec("{$this->isolate} --cleanup");
            $this->isolate = false;
        }
        return $this;
    }

    /**
     * Find an empty box
     *
     * @return integer
     */
    protected function findEmptyBox()
    {
        $id = 11;
        while(file_exists("/var/local/lib/isolate/{$id}")){
            $id++;
        }
        return $id;
    }
}
