<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/delete.php

delete run, course or shoe from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck()) {
	exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) :

	// get id
	$id = trim($_GET['id']);

	if (isset($_GET['item']) && in_array($_GET['item'], array('run', 'course', 'shoe'))) : 
		
		$item = $_GET['item'];
		
		switch ($item) {
			case 'run' :
				$table = 'runs';
				break;
			case 'course' :
				$table = 'courses';
				break;
			case 'shoe' :
				$table = 'shoes';
				break;
		}
		
		// check to see if item exists for that user
		$result = $conn->query('SELECT COUNT(*) FROM '.$table.' WHERE id = '.mysql_real_escape_string($id).' AND user = '.mysql_real_escape_string($user->ID()));
	
		if ($conn->getRow($result) == 0) {
			// error
			echo json_encode(array('status' => 'error', 'text' => 'there is no '.format($item).' with that ID'));

		} else {
			// delete shoe
			$conn->query('DELETE FROM '.$table.' WHERE id = '.mysql_real_escape_string($id).' AND user = '.mysql_real_escape_string($user->ID()));
			echo json_encode(array('status' => 'ok', 'id' => format($id), 'text' => 'removed '.format($item)));
		}
		
	else :
		// error
		echo json_encode(array('status' => 'error', 'text' => 'item type not provided or invalid'));
		
	endif;

else :
	// error
	echo json_encode(array('status' => 'error', 'text' => 'ID not provided or invalid'));
endif;
?>