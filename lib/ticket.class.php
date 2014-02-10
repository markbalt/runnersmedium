<?php

class TicketsComponent extends Component
{
	private $_conn = null;
	
	function __construct($connect = null)
	{
		$this->_conn = $connect;
	}
	
	public function setConection($connect = null)
	{
		$this->_conn = $connect;
	}
	
    // create a new ticket by providing the data to be stored in the ticket
    public function set($info = null)
    {
        $this->garbage();
        if ($info && $this->_conn->isConnected()) {
            
            $hash = md5(time());
            $created = date("Y-m-d H:i:s");
            $info = mysql_real_escape_string($info);
            
            // create new ticket
            $result = $this->_conn->query("INSERT INTO tickets (data, hash, created) VALUES ('$info', '$hash', '$created')");
            
            return $hash;
        }
        return false;
    }
    
    // return the value stored or false if the ticket can not be found
    public function get($ticket = null)
    {
        $this->garbage();
        if ($ticket && $this->_conn->isConnected()) {
        
        	// expire tickets after 24 hours
            $deadline = date('Y-m-d H:i:s', time() - (24 * 60 * 60));
            $ticket = mysql_real_escape_string($ticket);
            
            // select ticket contents
            $result = $this->_conn->query("SELECT id, data FROM tickets WHERE hash = '$ticket' AND created > '$deadline' LIMIT 1");

            if ($this->_conn->rowCount($result) > 0) {
            
                $line = $this->_conn->fetchAssoc($result);
                $ticket_id = $line['id'];
                $data = $line['data'];
        
                // auto-delete the ticket? $this->del($ticket);
                return $data;
            }
        }
        return false;
    }

    // delete a used ticket
    public function del($ticket = null)
    {
        $this->garbage();
        if ($ticket && $this->_conn->isConnected()) {
            $ticket = mysql_real_escape_string($ticket);
            
            // remove the ticket
            $this->_conn->query("DELETE FROM tickets WHERE hash = '$ticket'");
            return true;
        }
        return false;
    }

    // remove old tickets
    public function garbage()
    {
    	if ($this->_conn->isConnected()) {
    		// expire tickets after 24 hours
        	$deadline = date('Y-m-d H:i:s', time() - (24 * 60 * 60));
     		$this->_conn->query("DELETE FROM tickets WHERE created < '$deadline'");
     		return true;
     	}
     	return false;
    }
}

?>