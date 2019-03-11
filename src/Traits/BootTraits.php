<?php

namespace nazmulpcc\Traits;

/**
 * Boot traits
 */
trait BootTraits
{
    /**
     * Keep track of booted traits so they are not booted twice
     *
     * @var array
     */
    protected $booted = [];

    public function bootTraits()
    {
        foreach ($this->getClasses() as $class) {
            $this->bootTrait($class);
        }
        return $this;
    }
    
    /**
     * List of classes from which traits should be booted
     *
     * @return array
     */
    protected function getClasses()
    {
        $classes = array_values(class_parents($this));
        $classes[] = static::class;
        return $classes;
    }

    /**
	 * Boot all the traits from a given class
	 * @return void
	 */
	protected function bootTrait($class)
	{
		foreach (class_uses($class) as $trait) {
			$method = 'boot'. array_slice(explode('\\', $trait), -1)[0];
			if(!$this->hasBooted($trait) && method_exists($this, $method)){
				$this->{$method}();
            }
            $this->booted[$trait] = true;
		}
    }
    
    /**
     * Check if a trait has been booted
     *
     * @param string $trait
     * @return boolean
     */
    protected function hasBooted($trait)
    {
        return isset($this->booted[$trait]) && $this->booted[$trait];
    }
}
