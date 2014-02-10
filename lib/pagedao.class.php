<?php
/*

Runner's Medium
http://www.runnersmedium.com/

pagedao.class.php

runners medium specific database abstraction object

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

class PageDAOComponent extends DatabaseComponent
{	
	public function inviteExists($code)
	{
		if (!$this->conn) {
			return true;
		}

		// check if invite code exists
		if ($this->getRow($this->query('SELECT count(*) FROM invites WHERE redeemed = 0 AND code = \''.mysql_real_escape_string($code).'\' LIMIT 1')) == 0) {
			return false;
		}
		
		return true;
	}
	
	public function usernameExists($text)
	{
		if (!$this->conn) {
			return true;
		}

		// check if username exists
		if ($this->getRow($this->query('SELECT count(*) FROM users WHERE username = \''.mysql_real_escape_string($text).'\' LIMIT 1')) > 0) {
			return true;
		}
		
		// check reseved words
		if ($this->getRow($this->query('SELECT count(*) FROM reservedwords WHERE text = \''.mysql_real_escape_string($text).'\' LIMIT 1')) > 0) {
			return true;
		}
		
		return false;
	}
	
	public function emailExists($text)
	{
		if (!$this->conn) {
			return true;
		}

		// check if email exists
		if ($this->getRow($this->query('SELECT count(*) FROM users WHERE email = \''.mysql_real_escape_string($text).'\' LIMIT 1')) == 0) {
			return false;
		}
		
		return true;
	}
	
	public function getTypes()
	{
		// select all run types
		return $this->selectToArray('SELECT id, name FROM runtypes', 'id', 'name');
	}
	
	public function getCourses($userID = null)
	{
		if (!$userID) {
			return false;
		}
		
		// select all courses for this user
		return $this->selectToArray('SELECT id, name FROM courses WHERE user = '.mysql_real_escape_string($userID), 'id', 'name');
	}
	
	public function getShoes($userID = null)
	{
		if (!$userID) {
			return false;
		}
		
		// select all shoes for this user
		return $this->selectToArray('SELECT id, CONCAT(IFNULL(brand, \'\'), \' \', IFNULL(model, \'\')) AS name FROM shoes
			WHERE retired = 0 AND user = '.mysql_real_escape_string($userID), 'id', 'name');
	}
	
	// runs select and creates array with id and name columns
	public function selectToArray($thequery, $key, $value)
	{
		if (!$this->conn) {
			return false;
		}
		
		$arr = array();
		$result = $this->query($thequery);
		
		if ($this->rowCount($result) == 0) {
			return array();
		}
		
		// populate array
		while ($line = $this->fetchAssoc($result)) {
			$arr[$line[$key]] = $line[$value];
		}
		
		return $arr;
	}
	
	public function getDistance($courseID = null)
	{
		if (!$this->conn) {
			return false;
		}
		
		if (is_null($courseID)) {
			return false;
		}

		$result = $this->query('SELECT distance FROM courses WHERE id = '.mysql_real_escape_string($courseID).' LIMIT 1');

		if ($this->rowCount($result) == 0) {
			return null;
		} else {
			return $this->getRow($result);
		}
	}
	
	public function getDefaultCourse($userID = null)
	{
		if (!$this->conn) {
			return false;
		}
		
		if (!$userID) {
			return false;
		}

		// select most recently run course, if there are no runs select the newest course
		$result = $this->query('SELECT a.id, a.created, b.date FROM courses AS a LEFT JOIN runs AS b ON (a.id = b.course)
			WHERE a.user = '.mysql_real_escape_string($userID).' ORDER BY date DESC, created DESC LIMIT 1');

		if ($this->rowCount($result) > 0) {
			return $this->getRow($result);
		} else {
			return null;
		}
	}
	
	public function getDefaultShoe($userID = null)
	{
		if (!$this->conn) {
			return false;
		}
		
		if (!$userID) {
			return false;
		}

		// select most recently run shoe, if there are no runs select the newest shoe
		$result = $this->query('SELECT a.id, a.created, b.date FROM shoes AS a LEFT JOIN runs AS b ON (a.id = b.course)
			WHERE a.user = '.mysql_real_escape_string($userID).' ORDER BY date DESC, created DESC LIMIT 1');

		if ($this->rowCount($result) > 0) {
			return $this->getRow($result);
		} else {
			return null;
		}
	}
	
	public function getDefaultWeight($userID = null)
	{
		if (!$this->conn) {
			return false;
		}
		
		if (!$userID) {
			return false;
		}

		// select weight if one is available
		$result = $this->query('SELECT weight FROM users WHERE id = '.mysql_real_escape_string($userID));

		if ($this->rowCount($result) > 0) {
			return $this->getRow($result);
		} else {
			return null;
		}
	}
	
	public function deleteUser($userID)
	{
		// trigger takes care of referenced tables
		$this->query('DELETE FROM users WHERE id = '.mysql_real_escape_string($userID).' LIMIT 1');
	}
	
	public function isAdmin($userID)
	{
		if (!$this->conn) {
			return false;
		}
		
		if (!$userID) {
			return false;
		}

		// check for admin flag
		$result = $this->query('SELECT COUNT(*) FROM users WHERE id = '.mysql_real_escape_string($userID).'
			AND isadmin = 1 LIMIT 1');
			
		return $this->getRow($result);
	}
}

?>