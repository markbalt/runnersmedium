<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/nudge.php

nudge friend from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);
	
	// is this user the current user or does this user exist?
	if($id == $user->ID()) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'you cannot nudge yourself'));
		exit;
	} elseif ($conn->getRow($conn->query('SELECT COUNT(*) FROM users WHERE id = '.mysql_real_escape_string($id))) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'user does not exist'));
		exit;
	}
		
	// check to see if this user is a friend
	$result = $conn->query('SELECT COUNT(*) FROM friends
		WHERE (user = '.mysql_real_escape_string($user->ID()).' AND friend = '.mysql_real_escape_string($id).')
		OR (friend = '.mysql_real_escape_string($user->ID()).' AND user = '.mysql_real_escape_string($id).')');

	if ($conn->getRow($result) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'you can only nudge your friends'));

	} elseif ($conn->getRow($conn->query('SELECT COUNT(*) FROM nudges WHERE state IN (1,2) AND nudger = '.mysql_real_escape_string($user->ID()).' AND nudgee = '.mysql_real_escape_string($id).' AND created > SUBDATE(NOW(), INTERVAL 7 DAY)'))) {

		// user has already nudged this person in the past 1 week and has gone unaknowledged
		echo json_encode(array('status' => 'ok', 'text' => 'already nudged less than 1 week ago'));

	} else {
	
		// add nudge
		$conn->query('INSERT INTO nudges (nudger, nudgee, state) VALUES ('.mysql_real_escape_string($user->ID()).', '.mysql_real_escape_string($id).', 1)');
		echo json_encode(array('status' => 'ok', 'text' => 'nudged'));
	}
	
else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>