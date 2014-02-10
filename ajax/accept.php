<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/accept.php

accept friendship from ajax call

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
	$result = $conn->query('SELECT COUNT(*) FROM friends WHERE id = '.mysql_real_escape_string($id).' AND friend = '.mysql_real_escape_string($user->ID()).' AND state = 1');

	if ($conn->getRow($result) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'there is no request with that ID'));

	} else {

		// accept friendship
		$conn->query('UPDATE friends SET state = 2 WHERE id = '.mysql_real_escape_string($id).' AND friend = '.mysql_real_escape_string($user->ID()));
		echo json_encode(array('status' => 'ok', 'id' => format($id), 'text' => 'friendship accepted'));
	}
	
else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>