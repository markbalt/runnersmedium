<?php
/*

Runner's Medium
http://www.runnersmedium.com/

base.php

common page includes

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

session_start();

date_default_timezone_set('America/New_York');

require('server.php');
require('config.php');
require('component.class.php');
require('database.class.php');
require('pagedao.class.php');
require('user.class.php');
require('profile.class.php');
require('mail.class.php');
require('ticket.class.php');
require('file.class.php');
require('image.class.php');
require('ext.class.php');

$conn = new PageDAOComponent(true);
$user = new UserComponent();

// undo magic quotes, if necessary
if (get_magic_quotes_gpc()) {
	$_GET = stripslashesFromArray($_GET);
	$_POST = stripslashesFromArray($_POST);
	$_COOKIE = stripslashesFromArray($_COOKIE);
	$_REQUEST = stripslashesFromArray($_REQUEST);
}

// for validation
$message = null;
$error   = null;

// if user is not signed in, check for persistent login cookie
if (!$user->signinCheck()) {

	if (isset($_COOKIE['auth'])) {
	
		// refresh persistent login
		list($cookieid, $token) = explode(':', $_COOKIE['auth']);
		
		if (ctype_alnum($cookieid) && ctype_alnum($token)) {
		
			$salt = 'BALTRUSAITIS';

			$result = $conn->query('SELECT id, username, units FROM users WHERE token = \''.mysql_real_escape_string($token).'\' LIMIT 1');
			
			if ($conn->rowCount($result) == 1) {
					
				// token exists
				$line = $conn->fetchAssoc($result);

				if ($cookieid == md5($salt . md5($line['username'] . $salt))) {
	   
	   				// cookie id matches, create and store secondary identifier and token so we dont have to store username or password in the cookie
					$token = md5(uniqid(rand(), true));
					
					// update last login and store cookieid & token, kill two birds with one query
					$conn->query('UPDATE users SET lastlogin = CURDATE(), token = \''.mysql_real_escape_string($token).'\'
								WHERE id = '.mysql_real_escape_string($line['id']).' LIMIT 1');
					
					// set to expire in 1 week
					$timeout = time() + 60 * 60 * 24 * 7;
					
					// set the cookie
					setcookie('auth', $cookieid.':'.$token, $timeout);
									
			        // call user signin	
			        $user->signin($line['id'], $line['username'], $line['units']);

		        } else {
			    	// bad cookie, delete it
			    	setcookie('auth', 'DELETED!', time());
			    }
		    } else {
		    	// bad cookie, delete it
		    	setcookie('auth', 'DELETED!', time());
		    }
		}
	}

}

function URL()
{
    return URL;
}

function root()
{
    return ROOT;
}

function home()
{
    return ROOT.'home';
}

function signin()
{
    return ROOT.'signin';
}

function profile()
{
	return URL().'/';
}

function friends($user = null)
{
	if ($user) {
		return ROOT.$user.'/friends';
	} else {
		return ROOT.'friends';
	}
}

function lost()
{
    return ROOT.'lostpassword';
}

function password()
{
	// where does one reset their password?
    return ROOT.'settings/password';
}

function showError($err)
{
    die($err);
}

function redirect($whereto, $msg = null)
{
	if ($msg) {
		$_SESSION['msg'] = $msg;
	}
	
	header('Location: '.$whereto);
	exit;
}

function stripslashesFromArray($value)
{
    $value = is_array($value) ? array_map('stripslashesFromArray', $value) : stripslashes($value);
    return $value;
}

// format for html output
function format($text)
{
	return nl2br(htmlentities($text, ENT_QUOTES, 'UTF-8'));
}

// format distance - replace trailing zero
function format_d($text)
{
	$text = preg_replace('/0$/', '', nl2br(htmlentities($text, ENT_QUOTES, 'UTF-8')));
	
	if (empty($text)) {
		return 0;
	} else {
		return $text;
	}
}

// format time
function format_t($text)
{
	$text = preg_replace('/^(0|:){0,4}/', '', nl2br(htmlentities($text, ENT_QUOTES, 'UTF-8')));
	
	if (empty($text)) {
		return 0;
	} else {
		return $text;
	}
}

// format text area input - do not nl2br
function format_a($text)
{
	return htmlentities($text, ENT_QUOTES, 'UTF-8');
}

// returns true if value is set and is not empty 
function notempty($value, $trim = true)
{
    if ($trim) {
        $value = trim($value);
    }
    return (isset($value) && strlen($value));
}

// render an array as a series of select options
function arrayToSelect($option, $selected = null, $default = true)
{
	$theResult = null;

	if (is_null($selected)) {
		$theResult .= '<option value="" selected="selected">Select one</option>\n';
    }
    
    foreach ($option as $key => $value)
    {
		if (strcmp($key, $selected) == 0) {
			$theResult .= "<option selected=\"selected\" value=\"$key\">$value</option>\n";
		} else {
			$theResult .= "<option value=\"$key\">$value</option>\n";
		}
	}

    return $theResult;
}

function messages($error, $message)
{
	if (isset($error)) {
		echo '<div id="error"><span class="oops">Oops</span> '.$error.'</div>';
	} else if (isset($message)) {
		echo '<div id="message"><span class="ok">Okay</span> '.$message.'<a class="close" onclick="closeMessage();">x</a></div>';
	}
}

function zeroCheck($var)
{
	if (is_numeric($var)) {
		return $var;
	} else {
		return 0;
	}
}			
?>