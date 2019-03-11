<?php

namespace nazmulpcc\Traits;

trait Events {

	/**
	 * House of all events
	 * @var array
	 */
	protected $events = [];

	/**
	 * Register any $method that should be fired when $event happens
	 * @param  [type] $event  [description]
	 * @param  [type] $method [description]
	 * @return [type]       [description]
	 */
	public function registerEvent($event, $method)
	{
		if(!isset($this->events[$event])){
			$this->events[$event] = []; 
		}
		$this->events[$event][] = $method;
		return $this;
	}

	public function fireEvents($event, ...$args)
	{
		if (isset($this->events[$event])) {
			foreach ($this->events[$event] as $method) {
				$this->{$method}(...$args);
			}
		}
	}
}