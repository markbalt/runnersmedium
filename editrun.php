<?php
/*

Runner's Medium
http://www.runnersmedium.com/

editrun.php

view & udpate run info

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());

$stop   = false;
$prompt = null;

if (!isset($_GET['id']) || !notempty($_GET['id'])) :
	$error = 'run ID not provided';
	$stop = true;
elseif (!is_numeric($_GET['id'])) :
	$error = 'invalid run ID';
	$stop = true;
else :

	// id provided
	$runid = $_GET['id'];
	
	$hour = $min = $sec = $day = $month = $year = $type = $course = $shoe = $distance = $laps = $weight = $comments = null;
	
	// get lists
	$types   = $conn->getTypes();
	$courses = $conn->getCourses($user->ID());
	$shoes   = $conn->getShoes($user->ID());
	
	// add the none option to courses and shoes
	if (!empty($courses)) {
		$courses[0] = 'none';
	}
	
	if (!empty($shoes)) {
		$shoes[0] = 'none';
	}
		
	if (isset($_POST['action']) && $_POST['action'] == 'Save') :
	
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
			$mysql['runid']    = mysql_real_escape_string($runid);
			$mysql['userid']    = mysql_real_escape_string($user->ID());
		
			// time in seconds
			$mysql['time']    = mysql_real_escape_string($sec + ($min * 60) + ($hour * 3600));
			
			// update
			$conn->query("UPDATE runs SET date = '{$mysql['date']}', type = {$mysql['type']}, course = {$mysql['course']}, shoe = {$mysql['shoe']}, distance = {$mysql['dist']}, duration = SEC_TO_TIME({$mysql['time']}), weight = {$mysql['weight']}, comments = {$mysql['comments']}
						WHERE id = {$mysql['runid']} AND user = {$mysql['userid']}");
			
			$message = '<a href="'.root().'runs">Updated your run.  Click here to see all your runs.</a>';
		}
	else :
		// select contest name
		$result = $conn->query('SELECT UNIX_TIMESTAMP(date) AS date, type, course, shoe, distance, laps, duration, weight, comments FROM runs
								WHERE id = '.mysql_real_escape_string($runid).' AND user = '.mysql_real_escape_string($user->ID()));
		
		if ($conn->rowCount($result) > 0) {
		
			// grab data
			$line      = $conn->fetchAssoc($result);
			$date      = $line['date'];
			$type      = $line['type'];
			$course    = $line['course'];
			$shoe      = $line['shoe'];
			$distance  = $line['distance'];
			$laps      = $line['laps'];
			$weight    = $line['weight'];
			$comments  = $line['comments'];
			
			// parse time
			list($hour, $min, $sec) = explode(':', $line['duration']);
						
			// parse date
			if($date) {
				$month = date('n', $date);
				$day   = date('j', $date);
				$year  = date('Y', $date);
			}
			
			// calc distance by laps
			if ($laps > 1) {
				$distance = $distance/$laps;
			}
			
			// get shoe
			if (is_null($shoe)) {
				$shoe = 0;
			}
			
			if (is_null($course)) {
				$course = 0;
			}
			
		} else {
			$error = 'run not found';
			$stop = true;
		}
		
	endif; // Save run
	
endif; // id found

$title = 'Edit your '.format_d($distance * $laps).' '.format($user->getUnits(true)).' run';
require('header.php');
?>

<div id="content">
	
	<h2>Edit Run</h2>
	
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
				<input name="laps" id="laps" type="text" value="<?php echo format($laps); ?>" class="short" onkeyup="stats()" />
				
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
						?></span>
					</label>
					<textarea name="comments" id="comments" cols="100%" rows="10" onkeydown="text('comments')"><?php echo format_a($comments); ?></textarea>
				</div>
				
				<input name="action" type="submit" value="Save" class="button" id="logrun" />
				
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