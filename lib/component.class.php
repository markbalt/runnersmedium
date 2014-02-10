<?php

abstract class Component
{
	protected $error = null;
	
	public function __construct()
	{
		
	}
	
	public function __destruct()
	{
	
	}
	
	// show the last error
	public function showError()
	{
		return $this->error;
	}
}

?>