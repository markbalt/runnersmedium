<?php
/*

Runner's Medium
http://www.runnersmedium.com/

delete.php

delete user, originally designed to confirm and delete an arbitrary number of users

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck() || !$conn->isAdmin($user->ID())) {
	header('Location: '.signin());
	exit;
}


$stop = false;
$remove = array();

if (isset($_GET['action']) && $_GET['action'] == 'Delete selected') :
	
	if (isset($_GET['check-'.$user->ID()]) && $_GET['check-'.$user->ID()] == 'on') {
		$error = 'cannot delete admin';
		$stop  = true;
	} else {
		// select all ids
		$result = $conn->query('SELECT id FROM users WHERE id != '.mysql_real_escape_string($user->ID()));
		
		// loop through all available ids to see if they were selected
		while ($line = $conn->fetchAssoc($result)) {
			if (isset($_GET['check-'.$line['id']]) && $_GET['check-'.$line['id']] == 'on') {
				$remove[] = $line['id'];
			}
		}
				
		if (count($remove) == 0) {
			$error = 'no users selected';
			$stop  = true;
		} elseif (isset($_POST['action']) && $_POST['action'] == 'Confirm') {
			// delete list
			$conn->query('DELETE FROM users WHERE id IN ('.mysql_real_escape_string(implode($remove, ', ')).')');
			$message = 'removed user';
			
			if (count($remove) > 1) {
				$message .= 's';
			}
			
			$stop = true;
		} else {
			$result = $conn->query('SELECT username FROM users WHERE id IN ('.mysql_real_escape_string(implode($remove, ', ')).')');
		}
	}

else :
	$error = 'invalid delete parameter';
	$stop  = true;
endif; // delete selected


$title = 'Delete User';

if (count($remove) > 1) {
	$title .= 's';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

	<title>CMS - <?php echo $title; ?></title>
		
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

	<h2>Delete User<?php
		if (count($remove) > 1) {
			echo 's';
		}
	?>
	</h2>
	
	<?php
		// echo messages
		messages($error, $message);
		if (!$stop) :
		?>
			<div class="confirm">
				Are you sure you would like to delete the following user<?php
			if (count($remove) > 1) {
				echo 's';
			}?>?  This will also delete their runs, courses, shoes, friendships, likes etc.
		
				<ul>
					<?php
						while ($line = $conn->fetchAssoc($result)) {
							echo '<li>'.format($line['username']).'</li>';
						}
					?>
				</ul>
				
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