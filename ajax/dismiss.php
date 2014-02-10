<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/dismiss.php

dismiss a nudge from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);
		
	// check to see if nudge exists for current user
	$result = $conn->query('SELECT COUNT(*) FROM nudges
		WHERE id = '.mysql_real_escape_string($id).' AND nudgee = '.mysql_real_escape_string($user->ID()).' AND state IN (1, 2)');

	if ($conn->getRow($result) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'there is no nudge with that ID'));

	} else {

		// dismiss nudge
		$conn->query('UPDATE nudges SET state = 3 WHERE id = '.mysql_real_escape_string($id).' AND nudgee = '.mysql_real_escape_string($user->ID()));
		echo json_encode(array('status' => 'ok', 'id' => format($id), 'text' => 'nudge dismissed'));
	}
	
else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>