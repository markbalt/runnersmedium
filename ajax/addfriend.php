<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/addfriend.php

request friendship from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);
		
	// check to see if request exists for current user
	$result = $conn->query('SELECT COUNT(*) FROM friends
		WHERE user = '.mysql_real_escape_string($user->ID()).' AND friend = '.mysql_real_escape_string($id));

	if ($conn->getRow($result) > 0) {
		// error
		echo json_encode(array('status' => 'ok', 'text' => 'already requested'));

	} elseif($id == $user->ID()) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'you cannot be a friend with yourself'));

	} elseif ($conn->getRow($conn->query('SELECT COUNT(*) FROM users WHERE id = '.mysql_real_escape_string($id))) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'user does not exist'));

	} elseif ($conn->getRow($conn->query('SELECT COUNT(*) FROM friends WHERE state = 1 AND friend = '.mysql_real_escape_string($user->ID()).' AND user = '.mysql_real_escape_string($id)))) {

		// there is already a friend request from this user so just add the friend and skip the request
		$conn->query('UPDATE friends SET state = 2 WHERE user = '.mysql_real_escape_string($id).' AND friend = '.mysql_real_escape_string($user->ID()));
		echo json_encode(array('status' => 'ok', 'text' => 'friend added'));

	} else {
	
		// add request
		$conn->query('INSERT INTO friends (user, friend, state) VALUES ('.mysql_real_escape_string($user->ID()).', '.mysql_real_escape_string($id).', 1)');
		echo json_encode(array('status' => 'ok', 'text' => 'friend request sent'));
	}
	
else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>