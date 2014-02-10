<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/stats.php

get site stats from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

// get site stats
$result = $conn->query('SELECT COUNT(*) AS runs, ROUND(SUM(distance), 2) AS miles FROM runs');

if ($conn->rowCount($result) > 0) {

	// echo stats via ajax
	$line = $conn->fetchAssoc($result);
	echo json_encode(array('status' => 'ok', 'runs' => format($line['runs']), 'miles' => format($line['miles'])));

} else {
	
	// error
	echo json_encode(array('status' => 'error'));
}

?>