<?php
/*

Runner's Medium
http://www.runnersmedium.com/

account.php

view and edit user account settings

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signinCheck(signin());
$username = $user->username();

$email = $name = $timezone = $units = null;

// check for submit action
if (isset($_POST['action']) && $_POST['action'] == 'Save') {

	// get post data
	if (isset($_POST['username'])) {
		$username = $_POST['username'];
	}
	
	if (isset($_POST['email'])) {
		$email = $_POST['email'];
	}
	
	if (isset($_POST['name'])) {
		$name = $_POST['name'];
	}
	
	if (isset($_POST['timezone'])) {
		$timezone = $_POST['timezone'];
	}
	
	if (isset($_POST['units'])) {
		$units = $_POST['units'];
	}

    // was username updated?
    if ($user->username() != $username && (strlen($username) < 3 || strlen($username) > 15 || preg_match('/^\w+$/', $username) == 0)) {
		$error = 'invalid username';
	} elseif ($user->username() != $username && ($conn->usernameExists($username) && strtoupper($username) != strtoupper($user->username()))) {
		$error = 'username is taken.  Please choose another';
    } elseif ((strlen($email) < MIN_EMAIL || strlen($email) > MAX_EMAIL || eregi('^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$', $email) == 0)) {
        $error = 'invalid email';
    } elseif (strlen($name) == 0) {
        $error = 'please specify a name';
    } elseif (strlen($name) > MAX_NAME) {
        $error = 'name cannot be more than '.MAX_NAME.' characters';
    } elseif ($units != 0 && $units != 1) {
    	$error = 'invalid distance preference';
    } else {
        // update user info
        $sqlusername = mysql_real_escape_string($username);
        $sqlemail    = mysql_real_escape_string($email);
        $sqlname     = mysql_real_escape_string($name);
        $sqltimezone = mysql_real_escape_string($timezone);
        $sqlunits    = mysql_real_escape_string($units);
        
        // update user        
        $conn->query("UPDATE users SET username = '$sqlusername', email = '$sqlemail', name = '$sqlname', timezone = (SELECT id FROM timezones WHERE name = '$sqltimezone'), units = $sqlunits WHERE id = ".mysql_real_escape_string($user->ID())." LIMIT 1");
        $message = 'your account info has been saved';
        
        // update session data if username was changed
        if (strcmp($user->username(), $username) != 0) {
			$user->setUsername($username);
		}
		
		// update session data if units was changed
        if ($user->getUnits() != $units) {
			$user->setUnits($units);
		}
    }
} else {

	// select current account data
	$sqlusername = mysql_real_escape_string($username);
	$result = $conn->query("SELECT a.email, a.name, a.units AS units, b.name AS timezone FROM users AS a LEFT JOIN (timezones as b) ON (a.timezone = b.id) WHERE a.id = ".mysql_real_escape_string($user->ID())." LIMIT 1");

	if ($conn->rowCount($result) == 0) {
        $error = 'User session error';
    } else {
		$line = $conn->fetchAssoc($result);

		$email    = $line['email'];
		$name     = $line['name'];
		$timezone = $line['timezone'];
		$units    = $line['units'];
	}
}

$title = 'Runner\'s Medium - Account';
require('header.php');
?>

<div id="content">
	
	<ul id="tabnav">
		<li><a class="select" href="<?php echo root(); ?>settings/account">Account</a></li>
		<li><a href="<?php echo root(); ?>settings/editprofile">Edit Profile</a></li>
		<li><a href="<?php echo root(); ?>settings/picture">Picture</a></li>			
		<li><a href="<?php echo root(); ?>settings/password">Password</a></li>
		<li><a href="<?php echo root(); ?>settings/notices">Notices</a></li>
	</ul>
	<br class="clear" />
	
	<?php messages($error, $message); ?>
		
	<form action="" method="post" id="accountform">
		<fieldset>
			<label for="username">Username</label>
			<input name="username" id="username" type="text" onkeyup="userExists()" value="<?php echo format($username); ?>" />
			
            <div id="profileurl"><?php echo profile().'<strong>'.$user->username().'</strong>'; ?></div>
			
			<label for="email">Email</label>
			<input name="email" id="email" type="text" value="<?php echo format($email); ?>" />

			<label for="name">First and Last Name</label>
			<input name="name" id="name" type="text" value="<?php echo format($name); ?>" />
			
			<label for="timezone">Time Zone</label>
			<select name="timezone" id="timezone">
				<?php echo arrayToSelect($user->getTimeZones(), format($timezone)); ?>
			</select>

			<label for="units">Distance Preference</label>
			<select name="units" id="units">
				<?php echo arrayToSelect(array('0' => 'Miles', '1' => 'Kilometers'), format($units)); ?>
			</select>
			<input name="action" type="submit" value="Save" class="button" />
		</fieldset>
	</form>
	
	Are you looking to <a href="<?php echo root(); ?>settings/delete">delete</a> your account?

</div>

<?php

require('footer.php');

?>