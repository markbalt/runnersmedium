<?php

class DatabaseComponent extends Component
{
	protected $conn;
	protected $result;
	
	function __construct($connect = false)
	{
		if($connect) {
			$this->connect();
		}
	}
	
	// connect to database
	public function connect()
	{
		if ($this->conn) {
			return true;
		}
		
		$this->conn = @mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD) or $this->showError(mysql_error());
		@mysql_select_db(DATABASE_NAME) or $this->showError(mysql_error());
		return true;
	}
	
	public function disconnect()
	{
		if ($this->conn) {
			@mysql_close($this->conn);
			$this->conn = null;
		}
	}
	
	public function isConnected()
	{
		return ($this->conn != false);
	}
	
	public function query($theQuery)
	{
		if (!$this->conn) {
			return false;
		}
		
		$result = @mysql_query($theQuery, $this->conn) or $this->showError(mysql_error());
		
		return $result;
	}
	
	public function rowCount($result)
	{
		if (!$result) {
			return false;
		}
		
		if ($this->conn) {
			return @mysql_num_rows($result);
		} else {
			return false;
		}
	}
	
	public function fetchAssoc($result)
	{
		if (!$result) {
			return false;
		}
		
		if ($this->conn) {
			return @mysql_fetch_assoc($result);
		} else {
			return false;
		}
	}
	
	public function result($result, $targetRow, $targetColumn = "")
	{
		if (!$result) {
			return false;
		}
		
		if ($this->conn) {
			return @mysql_result($resultSet, $targetRow, $targetColumn);
		} else {
			return false;
		}		
	}
	
	public function getRow($result, $rowNum = 0)
	{
		if (!$result) {
			return false;
		}
		
		return @mysql_result($result, $rowNum);
	}
	
	public function showError($error)
	{
		// or die($error); for debugging purposes
		$this->error = $error;
		error_log('Database class error: '. $this->error);
		echo $error;
		return;
		// redirect to db error page
		header('Location: '.ROOT.'error');
		exit;
	}
}

?>