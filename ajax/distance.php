<?php
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/distance.php

get distance for user course from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);
		
	// get distance for that course
	$result = $conn->query('SELECT distance FROM courses
		WHERE id = '.mysql_real_escape_string($id).' AND user = '.mysql_real_escape_string($user->ID()).' LIMIT 1' );
	
	if ($conn->rowCount($result) == 0) {
	
		// error
		echo json_encode(array('status' => 'error', 'text' => 'there is no course with that ID'));

	} else {
		
		// add distance to xml
		$line = $conn->fetchAssoc($result);
		echo json_encode(array('status' => 'ok', 'id' => format($id), 'distance' => format_d($line['distance'])));
	}
	
else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>