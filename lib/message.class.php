<?php

class MessageHelper extends Component
{
	// members
	private $_data = array();
	
	function __construct($connect = false)
	{
		if($connect) {
			$this->connect();
		}
	}
	
	public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): ' . $name .' in ' . $trace[0]['file'] .' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }
    
    public function createMessage($tags)
    {
    	// copy current data
    	$result = $this->_data;

    	// check for keys to replace
    	foreach($tags as $key => $value) {
			$result = str_replace('%'.strtoupper($key).'%', $value, $result);
		}
		
		return $result;
    }
}
?>