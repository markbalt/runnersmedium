<?php
/*

Runner's Medium
http://www.runnersmedium.com/

reset.php

reset user password

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck() || !$conn->isAdmin($user->ID())) {
	header('Location: '.signin());
	exit;
}

$stop = false;

if (isset($_GET['id'])) :

	// get user id
    $userid = $_GET['id']; 
	$result = $conn->query('SELECT username FROM users WHERE id ='.mysql_real_escape_string($userid).' LIMIT 1');
	
	// does user exist?
	if ($conn->rowCount($result) > 0) {
	
		$line = $conn->fetchAssoc($result);
		$theuser = $line['username'];
		
		if (isset($_POST['action']) && $_POST['action'] == 'Confirm') {
			$newpassword = $user->generatePassword(8);
			$conn->query('UPDATE users SET password = MD5(\''.mysql_real_escape_string($newpassword).'\') WHERE id ='.mysql_real_escape_string($userid).' LIMIT 1');
			
			$message = format($theuser).'\'s password was reset to '.format($newpassword);
			$stop = true;
		}
		
	} else {
		$error = 'user with that ID not found';
		$stop  = true;
	}

else :
	$error = 'user ID not provided';
	$stop  = true;
endif; // id provided

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

	<title>CMS - reset password</title>
		
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Mark Baltrusaitis - josieprogramme.com" />

    <link rel="stylesheet" type="text/css" media="screen, projection" href="<?php echo root(); ?>cms/style.css" />
    
    <script src="<?php echo root(); ?>js/jquery/jquery-1.3.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/jquery/jquery.dimensions.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/jquery/jquery.tooltip.js" type="text/javascript"></script>
	
    <script src="<?php echo root(); ?>js/prototype-1.6.0.3.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/forms.js" type="text/javascript"></script>
	
</head>

<body>
	<ul id="nav">
		<li><a href="<?php echo root(); ?>">Back to Runner's Medium</a></li>
		<li><a href="<?php echo root(); ?>cms">CMS</a></li>
		<li><a href="<?php echo root(); ?>cms/users">Users</a></li>
	</ul>

	<h2>Reset User Password</h2>
	
	<?php
		// echo messages
		messages($error, $message);
		if (!$stop) :
		?>
			<div class="confirm">
				Are you sure you would like reset <?php echo format($theuser); ?>'s password?
				<fieldset>
					<form action="" method="POST" id="resetform" name="resetform">					
						<input value="Confirm" name="action" type="submit" class="button inline" />
						<input value="Cancel" type="button" class="button inline" onClick="parent.location='<?php echo root(); ?>cms/users'" />
					</form>
				</fieldset>
			</div>
						
	<?php endif; ?>
		
</body>
</html>