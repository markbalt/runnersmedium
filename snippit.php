<?php
/*

Runner's Medium
http://www.runnersmedium.com/

snippet.php

render user profile snapshot as blank javascript

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

if (!isset($_GET['user'])) {
	// just die if no user specified
	die('document.write(\'no user specified\')');
}

$thisPage = $_GET['user'];
$private  = true;

// check if user exists
$result = $conn->query('SELECT a.id, a.ispublic FROM users AS a WHERE a.username = \''.mysql_real_escape_string($thisPage).'\' LIMIT 1');

if($conn->rowCount($result) > 0) {

	$line = $conn->fetchAssoc($result);
	
	// check profile privacy
	if($line['ispublic'] == 0) {
		$private = true;
	} else {
	
		// public profile
		$private = false;
		
		// select profile data
		$result = $conn->query('SELECT a.ispublic, a.name, a.location, a.picture, a.units,
			(SELECT DATE_FORMAT(date, \'%a %b %e\') FROM runs WHERE user = a.id ORDER BY date DESC LIMIT 1) AS lastdate,
			(SELECT ROUND(distance, 2) FROM runs WHERE user = a.id ORDER BY date DESC LIMIT 1) AS last,
			ROUND(SUM(b.distance), 2) AS total, SEC_TO_TIME(SUM(TIME_TO_SEC(b.duration))/SUM(b.distance)) AS pace
			FROM users AS a LEFT JOIN (runs AS b) ON (a.id = b.user) WHERE a.username = \''.mysql_real_escape_string($thisPage).'\' LIMIT 1');

		// add data to profile component
		$line = $conn->fetchAssoc($result);
		$profile = new profileComponent($thisPage);
		
		// populate profile data
		foreach ($line as $key => $value) {
			$profile->$key = $value;
		}
		
		// user units preference
		if ($profile->units == '0') {
			$units = 'mi';
		} else {
			$units = 'km';		
		}
		
		// no runs logged yet
		if (!notempty($profile->last)) {
			$profile->last = 0;
			$profile->total = 0;
			$profile->pace = 0;
		}
	}
	
} else {
	die('document.write(\'user does not exist\')');
}
?>
document.write('<?php
		if ($private) :
				echo '<h2>'.format($thisPage).'</h2>';
				echo 'this profile is private';
		else :
		
			echo str_replace('\'','\\\'',$profile->showPicture());
			echo '<div class="username"><h2>'.format($thisPage).'</h2></div>';
			
			// replace any ' characters in the profile info with \'
			echo '<div class="info">'.format(str_replace('\'','\\\'',$profile->name)).' '.format(str_replace('\'','\\\'',$profile->location)).'</div>';
			
			echo '<div class="stats">';
				echo '<h3>last run '.format_d($profile->lastdate).'</h3><em>'.format_d($profile->last).'</em> <small>'.$units.'</small>';
				echo '<h3>total</h3><em>'.format_d($profile->total).'</em> <small>'.$units.'</small>';
				echo '<h3>ave pace</h3><em>'.format_t($profile->pace).'</em> <small>per '.$units.'</small>';
			echo '</div>';

		endif; // public profile
?>');