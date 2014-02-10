<?php
/*

Runner's Medium
http://www.runnersmedium.com/

newrun.php

log new run

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());

// stop flag
$stop  = false;

// todays date
$day   = date('j', time());
$month = date('n', time());
$year  = date('Y', time());

$type = $distance = $weight = $comments = $hour = $min = $sec = null;
$laps = 1;

// get defaults
$types   = $conn->getTypes();
$courses = $conn->getCourses($user->ID());
$course  = $conn->getDefaultCourse($user->ID());
$shoes   = $conn->getShoes($user->ID());
$shoe    = $conn->getDefaultShoe($user->ID());
$weight  = $conn->getDefaultWeight($user->ID());

// add the none option to courses and shoes
if (!empty($courses)) {
	$courses[0] = 'none';
}

if (!empty($shoes)) {
	$shoes[0] = 'none';
}

if (is_null($course)) {
	$distance = null;
} else {
	// get default distance for this course
	$distance = $conn->getDistance($course);
}

if (isset($_POST['action']) && $_POST['action'] == 'Log Run') :

	// get post data
	if (isset($_POST['day'])) {
		$day = $_POST['day'];
	}
	
	if (isset($_POST['month'])) {
		$month = $_POST['month'];
	}
	
	if (isset($_POST['year'])) {
		$year = $_POST['year'];
	}
	
	if (isset($_POST['type'])) {
		$type = $_POST['type'];
	}
	
	if (isset($_POST['course'])) {
		$course = $_POST['course'];
	}
	
	if (isset($_POST['shoe'])) {
		$shoe = $_POST['shoe'];
	}
		
	if (isset($_POST['distance'])) {
		$distance = $_POST['distance'];
	}
	
	if (isset($_POST['laps'])) {
		$laps = $_POST['laps'];
	}
	
	if (empty($laps)) {
		$laps = 1;
	}
	
	if (isset($_POST['hour'])) {
		$hour = $_POST['hour'];
	}

	if (isset($_POST['min'])) {
		$min = $_POST['min'];
	}
	
	if (isset($_POST['sec'])) {
		$sec = $_POST['sec'];
	}
	
	if (isset($_POST['weight'])) {
		$weight = $_POST['weight'];
	}

	if (isset($_POST['comments'])) {
		$comments = $_POST['comments'];
	}
	
	// validate
	if (!is_numeric($day) || !is_numeric($month) || !is_numeric($year) ) {
		$error = 'invalid run date';
	} elseif (!checkdate($month, $day, $year) ) {
		$error = 'your run date does not exist';
	} elseif (!array_key_exists($type, $types)) {
		$error = 'invalid run type';
	} elseif ($course != 0 && !array_key_exists($course, $courses)) {
		$error = 'invalid course';
	} elseif ($shoe != 0 && !array_key_exists($shoe, $shoes)) {
		$error = 'invalid shoes';
	} elseif (!is_numeric($distance) || !is_numeric($laps)) {
		$error = 'invalid distance';
	} elseif (($distance * $laps) > 1000 || ($distance * $laps) < 0) {
		$error = 'invalid distance';
	} elseif (!is_numeric($hour) && !is_numeric($min) && !is_numeric($sec) ) {
		$error = 'invalid duration';	
	} elseif (notempty($weight) && !is_numeric($weight)) { 
		$error = 'specify weight in lbs or kg only';
	} elseif (strlen($comments) > 90) {
		$error = 'comments text cannot be more than 90 characters';
	} else {
	
		$hour = zeroCheck($hour);
		$min  = zeroCheck($min);
		$sec  = zeroCheck($sec);
		
		// escape
		$mysql = array();
		
		$mysql['userid']   = mysql_real_escape_string($user->ID());
		$mysql['date']     = mysql_real_escape_string("$year-$month-$day");
		$mysql['type']     = mysql_real_escape_string($type);
		$mysql['course']   = ($course == 0) ? 'NULL' : mysql_real_escape_string($course);
		$mysql['shoe']     = ($shoe == 0) ? 'NULL' : mysql_real_escape_string($shoe);
		$mysql['dist']     = mysql_real_escape_string($distance * $laps);
		$mysql['weight']   = (notempty($weight)) ? mysql_real_escape_string($weight) : 'NULL';
		$mysql['comments'] = (notempty($comments)) ? '\''.mysql_real_escape_string($comments).'\'' : 'NULL';
		
		// time in seconds
		$mysql['time'] = mysql_real_escape_string($sec + ($min * 60) + ($hour * 3600));
		
		// insert
		$conn->query("INSERT INTO runs (user, date, type, course, shoe, distance, duration, weight, comments)
			VALUES ({$mysql['userid']}, '{$mysql['date']}', {$mysql['type']}, {$mysql['course']}, {$mysql['shoe']}, {$mysql['dist']}, SEC_TO_TIME({$mysql['time']}), {$mysql['weight']}, {$mysql['comments']})");
		
		// redirect on success
		redirect(root().'runs', 'Logged your run.');
	}
	
else :
	
	if (isset($_GET['course']) && is_numeric($_GET['course'])) {
		
		// does this course id exist?
		$result = $conn->query('SELECT id, distance FROM courses WHERE id = '.mysql_real_escape_string($_GET['course']).' AND user = '.mysql_real_escape_string($user->ID()));
		
		if ($conn->rowCount($result) == 0) {
			$error = 'course id does not exist';
		} else {
		
			// course info
			$line = $conn->fetchAssoc($result);
			$course = $line['id'];
			$distance = $line['distance'];
		}
	}
	
	if (isset($_GET['shoes']) && is_numeric($_GET['shoes'])) {
		
		// does this shoe id exist?
		$result = $conn->query('SELECT COUNT(id) FROM shoes WHERE id = '.mysql_real_escape_string($_GET['shoes']).' AND user = '.mysql_real_escape_string($user->ID()));
		
		if ($conn->getRow($result) == 0) {
			$error = 'shoe id does not exist';
		} else {
			$shoe = $_GET['shoes'];
		}
	}

endif; // log run

$title = 'Log your '.format_d($distance * $laps).' '.format($user->getUnits(true)).' run';
require('header.php');
?>

<div id="content">
	
	<h2>New Run</h2>
		
	<?php
		messages($error, $message);
		if (!$stop) : ?>

		<form action="" method="post" id="newrun">
			<fieldset>
				<label for="month">Run Date</label>
				
				<select name="month" id="month">
				<?php echo arrayToSelect($user->getMonths(), format($month)); ?>
				</select>
				<select name="day" id="day">
					<?php echo arrayToSelect($user->getDays(), format($day)); ?>
				</select>
				<select name="year" id="year">
					<?php echo arrayToSelect($user->getYears(), format($year)); ?>
				</select>
				
				<label for="type">Type</label>
				<select name="type" id="type">
					<?php echo arrayToSelect($types, format($type)); ?>
				</select>
				
				<?php if (!empty($courses)) : ?>
					<label for="course">Course</label>
					<select name="course" id="course" onchange="changeCourse()">
						<?php echo arrayToSelect($courses, format($course)); ?>
					</select>
				<?php endif; ?>

				<?php if (!empty($shoes)) : ?>
					<label for="shoe">Shoes</label>
					<select name="shoe" id="shoe">
						<?php echo arrayToSelect($shoes, format($shoe)); ?>
					</select>
				<?php endif; ?>
				
				<br class="clear" />
				
				<label for="distance" class="inline">Distance<br />
				<input name="distance" id="distance" type="text" value="<?php echo format($distance); ?>" class="short" onkeyup="stats()" />
				</label>
				
				<label for="laps">Laps<br /></label>
				<input name="laps" id="laps" type="text" value="<?php echo format($laps); ?>" class="short" onkeyup="stats()" onblur="if (this.value == '' || this.value == '0') this.value = '1'; stats()" />
				<br class="clear" />
				
				<label for="hour">Duration</label>
				<span class="time">
					<input name="hour" id="hour" type="text" value="<?php echo format($hour); ?>" class="time" />
					<label for="hour" class="hint">Hour</label>
				</span>
				
				<span class="time">
					<input name="min" id="min" type="text" value="<?php echo format($min); ?>" class="time" />
					<label for="min" class="hint">Min</label>
				</span>
				
				<span class="time">
					<input name="sec" id="sec" type="text" value="<?php echo format($sec); ?>" class="time" />
					<label for="sec" class="hint">Sec</label>
				</span>
				<br class="clear" />
				
				<a id="showmore" onclick="showMore()">more</a>
				<br class="clear" />
				
				<div id="more" style="display:none;">
					<label for="weight">Weight</label>
					<input name="weight" id="weight" type="text" value="<?php echo format($weight); ?>" class="short" />
	
					<label for="comments">Comments <span id="chars"><?php	
						$chars = 90-strlen(str_replace("\r\n", "\n", $comments));
						if ($chars < 90) {
							if ($chars < 0 ) {
								echo '<span class="error">'.$chars.'</span> characters remaining';
							} else {
								echo $chars.' characters remaining';
							}
						}
						?></span></label>
					<textarea name="comments" id="comments" cols="100%" rows="10" onkeydown="text('comments')"><?php echo format_a($comments); ?></textarea>
				</div>
				<input type="hidden" name="myunits" id="myunits" value="<?php echo format($user->getUnits(true)); ?>" />
				<input name="action" type="submit" value="Log Run" class="button" id="logrun" />
				
			</fieldset>
		</form>

		<?php
			// show more if there are
			if (!strcmp($comments, '') == 0) :
				$scripts = <<<EOD
<script type="text/javascript">
jQuery(function() {
	showMore();
});
</script>
EOD;
			endif;
		endif; // no errors
	?>
</div>

<?php
require('footer.php');
?>