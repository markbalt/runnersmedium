<?php
/*

Runner's Medium
http://www.runnersmedium.com/

password.php

update user password

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signinCheck(signin());

$newpassword1 = $newpassword2 = null;

if (isset($_POST['action']) && $_POST['action'] == 'Save') {   
 
    // get post data
    if (isset($_POST['newpassword1'])) {
		$newpassword1 = $_POST['newpassword1'];
	}
    
    if (isset($_POST['newpassword2'])) {
		$newpassword2 = $_POST['newpassword2'];
	}
    
    // get current password
    $result = $conn->query('SELECT password FROM users WHERE password = MD5(\''.mysql_real_escape_string($newpassword1).'\') AND id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');
    	
	// validate new passwords
	if (strcmp($newpassword1, $newpassword2) != 0) {
		$error = 'new passwords do not match';
	} elseif (strcmp($newpassword1, $user->username()) == 0) {
		$error = 'new password must be different from your username';
	} elseif ($conn->rowCount($result) == 1) {
		$error = 'new password must be different from original password';
	} elseif(strlen($newpassword1) < MIN_PASSWORD || strlen($newpassword1) > MAX_PASSWORD) {
		$error = 'password must be between '.MIN_PASSWORD.' and '.MAX_PASSWORD.' characters';
	} else {
		
		// update password
		$conn->query('UPDATE users SET password = MD5(\''.mysql_real_escape_string($newpassword1).'\') WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');
		$message = 'your password has been updated';
	}

}

$title = 'Runner\'s Medium - Update Password';
require('header.php');
?>

<div id="content">
	
	<ul id="tabnav">
		<li><a href="<?php echo root(); ?>settings/account">Account</a></li>
		<li><a href="<?php echo root(); ?>settings/editprofile">Edit Profile</a></li>
		<li><a href="<?php echo root(); ?>settings/picture">Picture</a></li>			
		<li><a class="select" href="<?php echo root(); ?>settings/password">Password</a></li>
		<li><a href="<?php echo root(); ?>settings/notices">Notices</a></li>
	</ul>
	<br class="clear" />
	
	<?php messages($error, $message); ?>
		
	<form action="" method="post" id="passwordform">
		<fieldset>
			<label for="newpassword1">New Password</label>
			<input type="password" name="newpassword1" id="newpassword1" />

			<label for="newpassword2">Confirm New Password</label>
			<input type="password" name="newpassword2" id="newpassword2" />
				
			<input value="Save" name="action" type="submit" class="button" />
		</fieldset>
	</form>

</div>

<?php
require('footer.php');
?>