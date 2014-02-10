<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/removefriend.php

remove friend from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);
		
	// check to see if friend exists for current user and grab friend's username
	$result = $conn->query('SELECT b.username FROM friends AS a JOIN users AS b ON (a.user = b.id)
		WHERE a.id = '.mysql_real_escape_string($id).' AND friend = '.mysql_real_escape_string($user->ID()).'
		UNION SELECT b.username from friends AS a JOIN users AS b ON (a.friend = b.id)
		WHERE a.id = '.mysql_real_escape_string($id).' AND user = '.mysql_real_escape_string($user->ID()));

	if ($conn->rowCount($result) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'friend ID not found'));
		
	} else {
	
		$line = $conn->fetchAssoc($result);
		
		// add request
		$conn->query('DELETE FROM friends WHERE id = '.mysql_real_escape_string($id));
		echo json_encode(array('status' => 'ok', 'id' => format($id), 'text' => 'you are no longer friends with '.$line['username']));
	}

else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>