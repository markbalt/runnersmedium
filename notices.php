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

$friends = $nudges = $updates = null;

// check for submit action
if (isset($_POST['action']) && $_POST['action'] == 'Save') {

	// get post data
	if (isset($_POST['friends']) && $_POST['friends'] == '1') {
		$friends = '1';
	} else {
		$friends = '0';
	}
	
	if (isset($_POST['nudges']) && $_POST['nudges'] == '1') {
		$nudges = '1';
	} else {
		$nudges = '0';
	}
	
	if (isset($_POST['updates']) && $_POST['updates'] == '1') {
		$updates = '1';
	} else {
		$updates = '0';
	}

	// update
	$conn->query('UPDATE users SET optin_friends = '.mysql_real_escape_string($friends).',
		optin_nudges = '.mysql_real_escape_string($nudges).',
		optin_updates = '.mysql_real_escape_string($updates).'
		WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');
		
	// success
	$message = 'your notification settings have been saved';

} else {

	// select current account data
	$result = $conn->query("SELECT optin_friends AS friends, optin_nudges AS nudges, optin_updates AS updates
		FROM users WHERE id = ".mysql_real_escape_string($user->ID())." LIMIT 1");

	if ($conn->rowCount($result) == 0) {
        $error = 'User session error';
    } else {
    
    	// current settings
		$line    = $conn->fetchAssoc($result);
		$friends = $line['friends'];
		$nudges  = $line['nudges'];
		$updates = $line['updates'];
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
		<li><a href="<?php echo root(); ?>settings/password">Password</a></li>
		<li><a class="select" href="<?php echo root(); ?>settings/notices">Notices</a></li>
	</ul>
	<br class="clear" />
	
	
	<?php messages($error, $message); ?>
		
	<form action="" method="post" id="noticesform">
		<fieldset>
		
			<br class="clear" />
			<?php if ($friends) {
				echo '<input name="friends" id="friends" type="checkbox" class="check" value="1" checked="checked" />';
			} else {
				echo '<input name="friends" id="friends" type="checkbox" class="check" value="1" />';
			} ?>
			<label for="friends" class="check">Email me when someone requests my friendship</label>
		
			<br class="clear" />
			<?php if ($nudges) {
				echo '<input name="nudges" id="nudges" type="checkbox" class="check" value="1" checked="checked" />';
			} else {
				echo '<input name="nudges" id="nudges" type="checkbox" class="check" value="1" />';
			} ?>
			<label for="nudges" class="check">Email me when a friend nudges me</label>
			
			<br class="clear" />
			<?php if ($updates) {
				echo '<input name="updates" id="updates" type="checkbox" class="check" value="1" checked="checked" />';
			} else {
				echo '<input name="updates" id="updates" type="checkbox" class="check" value="1" />';
			} ?>
			<label for="updates" class="check">Keep me updated with Runner's Medium news</label>

			<input name="action" type="submit" value="Save" class="button" />
		</fieldset>
	</form>

</div>

<?php
require('footer.php');
?>