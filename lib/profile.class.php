<?php
/*

Runner's Medium
http://www.runnersmedium.com/

profile.class.php

class to manage and render profile data

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

class profileComponent extends Component
{
	// members
	private $_data = array();
	
	public function __construct($username = null)
	{
		$this->username = $username;
	}
	
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): ' . $name .' in ' . $trace[0]['file'] .' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    public function __isset($name) {
        return isset($this->_data[$name]);
    }

    public function __unset($name) {
        unset($this->_data[$name]);
    }
	
	public function showPicture()
	{
		// return user picture
		$path = null;
		$user = null;
		
		if (notempty($this->picture)) {
			$path = root().PIC_DIR.$this->picture;
		} else {
			$path = root().DEFAULT_PIC;
		}
		
		if (notempty($this->username)) {
			$user = $this->username;
		}
		
		return '<img src="'.format($path).'" alt="'.format($user).'\'s picture" class="picture" />';
	}
}

?>