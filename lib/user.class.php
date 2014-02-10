<?php

class UserComponent extends Component
{
	// members
	private $_userid   = null;
	private $_username = null;
	private $_userauth = false;
	private $_units    = 0;
	
	public function __construct()
	{
		if (isset($_SESSION['rm_userauth']) && $_SESSION['rm_userauth'] == true) {
		
			// set some session variables for easy access
			$this->_userid   = $_SESSION['rm_userid'];
			$this->_username = $_SESSION['rm_username'];
			$this->_units    = $_SESSION['rm_units'];
		}
	}
	
	// update session username
	public function setUsername($newUsername = null)
	{
		if($newUsername) {
			$this->_username         = $newUsername;
			$_SESSION['rm_username'] = $newUsername;
		}
	}
	
	public function signIn($newrm_userid = null, $newusername = null, $redirectTo = null, $units = 0)
	{
		if($newrm_userid && $newusername) {
			// set the session
			$_SESSION['rm_userid']   = $newrm_userid;
			$_SESSION['rm_username'] = $newusername;
			$_SESSION['rm_userauth'] = true;
			$_SESSION['rm_units']    = $units;
			
			$this->_userid   = $newrm_userid;
			$this->_username = $newusername;
			$this->_userauth = true;
			$this->_units    = $units;
			
			if($redirectTo) {
				// after signin redirect to home
				header('Location: '.$redirectTo);
				exit;
			}
			return true;
		}
		return false;
	}
	
	public function signOut($redirectTo = null)
	{
		$this->_userid   = null;
		$this->_username = null;
		$this->_userauth = false;
		$this->_userauth = null;
		
		// if the user is signed in, unset the session
		if (isset($_SESSION['rm_userauth'])) {
			unset($_SESSION['rm_userauth']);
		}
		
		if (isset($_SESSION['rm_userid'])) {
			unset($_SESSION['rm_userid']);
		}
		
		if (isset($_SESSION['rm_username'])) {
			unset($_SESSION['rm_username']);
		}
		
		if (isset($_SESSION['rm_units'])) {
			unset($_SESSION['rm_units']);
		}
		
		if (isset($_COOKIE['auth'])) {
			setcookie('auth', 'DELETED!', time());
		}
		
		if ($redirectTo) {
			// after signin redirect to home
			header('Location: '.$redirectTo);
			exit;
		}
		return true;
	}
	
	// check if user is authenticated, if not redirect if specified
	public function signinCheck($redirectTo = null)
	{
		if (!isset($_SESSION['rm_userauth']) || $_SESSION['rm_userauth'] != true) {
			if($redirectTo) {
				// not signed in, redirect to signin page
				header("Location: $redirectTo");
				exit;
			}
			return false;
		}
		return true;
	}

	public function username()
	{
		if($this->_username) {
			return $this->_username;
		} else {
			return false;
		}
	}
	
	public function ID()
	{
		if($this->_userid) {
			return $this->_userid;
		} else {
			return false;
		}
	}
	
	// miles or kilometers
	public function getUnits($dist = false)
	{
		if(isset($this->_units)) {
			
			// return string for us or metric
			if ($dist) {
				if ($this->_units == '0') {
					return 'mi';
				} else {
					return 'km';		
				}
			} else {
				return $this->_units;
			}
		} else {
			return false;
		}
	}
	
	// update unit
	public function setUnits($new = null)
	{
		if($new == 0 || $new == 1) {
			$this->_units = $new;
			$_SESSION['rm_units'] = $new;
		}
		return false;
	}
	
	// return an array of time zones as key/value pair
	public function getTimeZones()
	{
		return array(
			'Eastern Time (US &amp; Canada)' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
			'Central Time (US &amp; Canada)' => '(GMT-06:00) Central Time (US &amp; Canada)',
			'Mountain Time (US &amp; Canada)' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
			'Pacific Time (US &amp; Canada)' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
			'Alaskan Time' => '(GMT-09:00) Alaskan Time',
			'Hawaii-Aleutians Time' => '(GMT-10:00) Hawaii-Aleutians Time',
			'International Date Line East' => '(GMT+12:00) International Date Line East',
			'Magadan Time Russia' => '(GMT+11:00) Magadan Time Russia',
			'East Australian Time' => '(GMT+10:00) East Australian Time',
			'Central Australian Time' => '(GMT+09:30) Central Australian Time',
			'Japan Time' => '(GMT+09:00) Japan Time',
			'West Australian Time' => '(GMT+08:00) West Australian Time',
			'China Coast Time' => '(GMT+08:00) China Coast Time',
			'North Sumatra' => '(GMT+06:30 North Sumatra',
			'Russian Federation Zone 5' => '(GMT+06:00) Russian Federation Zone 5',
			'Indian' => '(GMT+05:30 Indian',
			'Russian Federation Zone 4' => '(GMT+05:00) Russian Federation Zone 4',
			'Russian Federation Zone 3' => '(GMT+04:00) Russian Federation Zone 3',
			'Iran' => '(GMT+03:30 Iran',
			'Baghdad Time/Moscow Time' => '(GMT+03:00) Baghdad Time/Moscow Time',
			'Eastern Europe Time' => '(GMT+02:00) Eastern Europe Time',
			'Central European Time' => '(GMT+01:00) Central European Time',
			'Universal Time Coordinated' => '(GMT+00:00) Universal Time Coordinated',
			'West Africa Time' => '(GMT-01:00) West Africa Time',
			'Azores Time' => '(GMT-02:00) Azores Time',
			'Atlantic Time' => '(GMT-03:00) Atlantic Time',
			'Newfoundland' => '(GMT-03:30 Newfoundland'
			);
	}
	
	// return an array of genders as key/value pair
	public function getGenders()
	{
		return array(
			'Select one' => 'Select one',
			'GIRL' => 'Girl',
			'GUY' => 'Guy',
			);
	}
	
	// return an array of months as key/value pair
	public function getMonths($label = false)
	{
		$result = array();
		
		if ($label) {
			$result = array('Month' => 'Month');
		}
		
		$result += array('1' => 'Jan',
			'2' => 'Feb',
			'3' => 'Mar',
			'4' => 'Apr',
			'5' => 'May',
			'6' => 'Jun',
			'7' => 'Jul',
			'8' => 'Aug',
			'9' => 'Sep',
			'10' => 'Oct',
			'11' => 'Nov',
			'12' => 'Dec'
			);
		
		return $result;
	}
	
	// return an array of days as key/value pair
	public function getDays($label = false)
	{
		$result = array();
		
		if ($label) {
			$result = array('Day' => 'Day');
		}
		
		$result += array('1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12',
			'13' => '13',
			'14' => '14',
			'15' => '15',
			'16' => '16',
			'17' => '17',
			'18' => '18',
			'19' => '19',
			'20' => '20',
			'21' => '21',
			'22' => '22',
			'23' => '23',
			'24' => '24',
			'25' => '25',
			'26' => '26',
			'27' => '27',
			'28' => '28',
			'29' => '29',
			'30' => '30',
			'31' => '31'
			);
			
		return $result;
	}
	
	// return an array of years as key/value pair starting starting 13 years ago and ending at 1899
	public function getPastYears($label = false)
	{
		$start = date('Y', time())-13;
		$stop = 1899;
	
		// array header for select box
		if ($label) {
			$years = array('Year' => 'Year');
		} else {
			$years = array();
		}
		
		for ($i = $start; $i >= $stop; $i--) {
			$years[$i] = $i;
		}
		
		return $years;
	}
	
	// return an array of years as key/value pair starting starting 5 years back and ending with this year 
	public function getYears($label = false)
	{
		$start = date('Y', time()) - 5;
		$stop = date('Y', time());
		
		// array header for select box	
		if ($label) {
			$years = array('Year' => 'Year');
		} else {
			$years = array();
		}
		
		for ($i = $start; $i <= $stop; $i++) {
			$years[$i] = $i;
		}
		
		return $years;
	}

	// return profile javascript snippet
	public function getSnippet()
	{
		if(isset($_SESSION['rm_username'])) {
			return '<script type="text/javascript" src="'.profile().$_SESSION['rm_username'].'/js"></script>';
		} else {
			return false;
		}
	}
	
	// return profile path
	public function profile()
	{
		if(isset($_SESSION['rm_username'])) {
			return profile().$_SESSION['rm_username'];
		} else {
			return profile();
		}
	}
	
	// get xhtml for profile picture
	public function getAnyPicture($user = null, $picture = null)
	{	
		if ($picture) {
			$path = root().PIC_DIR.$picture;
		} else {
			$path = root().DEFAULT_PIC;
		}
		
		return '<img src="'.format($path).'" alt="'.format($user).'\'s picture" class="picture" />';
	}
	
	// calculate calories burned on a run
	public function calcRunCals($dist, $min, $weight)
	{
		if ($min == 0) {
			return;
		}
		
		// convert to km
		if ($this->getUnits() == 0) {
			$dist = $dist * 1.609344;
			$weight = $weight * 0.45359237;
		}
		
		// pace in km/hr
		$pace = $dist / ($min / 60);
		$coef = 0.01713324; // estimation coeficient for running
		$m = $coef * $pace; // calories / minute / kg
		
		return round($m * $min * $weight, 0); // calories
	}
	
	// generate a random password
	public function generatePassword($length = 8)
	{	
		// define possible characters
		$possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdfghjkmnpqrstvwxyz"; 
		$password = '';
		
		// seed clock
		srand((double)microtime()*1000000);
		$i = 0; 
		
		// add random characters to $password until $length is reached
		while ($i < $length) { 
		
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			    
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) { 
				$password .= $char;
				$i++;
			}
		
		}

		return $password;
	}
}

?>