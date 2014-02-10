<?php
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/retire.php

retire shoe from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);
		
	// check to see if shoe exists for that user
	$result = $conn->query('SELECT COUNT(*) FROM shoes WHERE id = '.mysql_real_escape_string($id).' AND user = '.mysql_real_escape_string($user->ID()));

	if ($conn->getRow($result) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'there is no shoe with that ID'));

	} else {
		// retire shoe
		$conn->query('UPDATE shoes SET retired = 1 WHERE id = '.mysql_real_escape_string($id).' AND user = '.mysql_real_escape_string($user->ID()));
		echo json_encode(array('status' => 'ok', 'id' => format($id), 'text' => 'retired shoe'));
	}
	
else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>