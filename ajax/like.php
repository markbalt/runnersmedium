<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/like.php

toggle like from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);
	
	// check if run exists
	$result = $conn->query('SELECT COUNT(*) FROM runs WHERE id = '.mysql_real_escape_string($id).' LIMIT 1');

	if ($conn->getRow($result) == 0) {
		// error
		echo json_encode(array('status' => 'error', 'text' => 'run does not exist'));

	} else {
		
		// get like value
		$line = $conn->fetchAssoc($conn->query('SELECT id, IF(id, 1, 0) AS liked FROM likes
			WHERE user = '.mysql_real_escape_string($user->ID()).' AND run = '.mysql_real_escape_string($id).' LIMIT 1'));
	
		// insert or update like as needed
		if ($line['liked']) {
			$conn->query('DELETE FROM likes WHERE id = '.mysql_real_escape_string($line['id']));
			echo json_encode(array('status' => 'ok', 'id' => format($id), 'text' => 'like removed'));
		} else {
			$conn->query('INSERT INTO likes (user, run) VALUES ('.mysql_real_escape_string($user->ID()).', '.mysql_real_escape_string($id).')');
			echo json_encode(array('status' => 'ok', 'id' => format($id), 'text' => 'like added'));
		}
	}
	
else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>