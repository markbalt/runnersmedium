<?php
/*

Runner's Medium
http://www.runnersmedium.com/

editcourse.php

view & update course info and map route

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());
$stop = false;
$prompt = null;

// user
$theid = mysql_real_escape_string($user->ID());

$name = $city = $distance = $comments = $params = $location = null;

if (!isset($_GET['id']) || !notempty($_GET['id'])) :
	$error = 'course ID not provided';
	$stop = true;
elseif (!is_numeric($_GET['id'])) :
	$error = 'invalid course ID';
	$stop = true;
else :
	
	// id provided
	$courseid = $_GET['id'];
	
	if (isset($_POST['action']) && $_POST['action'] == 'Save') {
	
		// get post data
		if (isset($_POST['name'])) {
			$name = $_POST['name'];
		}
		
		// give the course a generic name
		if (strlen($name) < 1) {
			$name = 'My Course';
		}
		
		if (isset($_POST['city'])) {
			$city = $_POST['city'];
		}
		
		if (isset($_POST['distance'])) {
			$distance = $_POST['distance'];
		}
		
		if (isset($_POST['comments'])) {
			$comments = str_replace("\r\n", "\n",$_POST['comments']);
		}
		
		if (isset($_POST['params'])) {
			$params = $_POST['params'];
		}
		
		// validate
		if (strlen($name) > 32) {
			$error = 'name must be less than 32 characters';
		} elseif (strlen($city) < 1) {
			$error = 'Enter the city where your course is located';
		} elseif (strlen($city) > 55) {
			$error = 'city must be less than 55 characters';
		} elseif (!is_numeric($distance)) {
			$error = 'you must enter the distance for your course';
		} elseif ($distance < 0 || $distance > 10000) {
			$error = 'invalid distance';
		} elseif (strlen($comments) > 90) {
			$error = 'comments cannot be more than 90 characters';
		}  elseif (strlen($params) > 65535) {
			$error = 'course error';
		} else {
	
			// escape
			$mysql['userid']   = mysql_real_escape_string($user->ID());
			$mysql['courseid'] = mysql_real_escape_string($courseid);
			$mysql['name']     = mysql_real_escape_string($name);
			$mysql['dist']     = mysql_real_escape_string($distance);
			$mysql['city']     = mysql_real_escape_string($city);
			$mysql['comments'] = (notempty($comments)) ? '\''.mysql_real_escape_string($comments).'\'' : 'NULL';
			$mysql['params']   = mysql_real_escape_string($params);

			// update
			$conn->query("UPDATE courses SET name = '{$mysql['name']}', distance = {$mysql['dist']}, city = '{$mysql['city']}', comments = {$mysql['comments']}, params = '{$mysql['params']}'
						WHERE user = {$mysql['userid']} AND id = {$mysql['courseid']}");
	        
			// run it?
			$message = '<a href="'.root().'newrun?course='.format($courseid).'">Successfully updated course.  Click here to run it.</a>';
		}
	} else {
	
		// select contest name
		$result = $conn->query('SELECT name, distance, city, comments, params FROM courses
								WHERE id = '.mysql_real_escape_string($courseid).' AND user = '.mysql_real_escape_string($user->ID()));
		
		if ($conn->rowCount($result) > 0) {
		
			// grab data
			$line      = $conn->fetchAssoc($result);
			$name      = $line['name'];
			$distance  = $line['distance'];
			$city      = $line['city'];
			$comments  = $line['comments'];
			$params    = $line['params'];
			
		} else {
			$error = 'course not found';
			$stop = true;
		}
	}

endif; // id provided

if (!$stop) {
	$root = root();
	$gmapKey = GMAP_KEY;

	$scripts = <<<EOD
<script type="text/javascript" src="{$root}js/tabs.min.js"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$gmapKey}" type="text/javascript"></script>
<script src="{$root}js/gmap.min.js" type="text/javascript"></script>

<script type="text/javascript">
	
	jQuery(document).ready( 
		function (){
			jQuery('input').keypress(function (event){ return event.keyCode == 13 ? false : true; });
			
			jQuery("#location").keyup(function(e){
				var code = (e.keyCode ? e.keyCode : e.which);
				if(code == 13) {
					showAddress($('location').value);
				}
			});
	   }
	);

</script>

EOD;

	$body = 'onload="initialize()" onunload="GUnload()"';
}

$title = 'Runner\'s Medium - Edit ';
if (is_null($name)) {
	$title .= 'course';
} else {
	$title .= format($name);
}

require('header.php');
?>

<div id="content">

	<?php if (!$stop) : ?>
		<ul id="tabnav">
			<li><a class="select" href="#gmapform">Map</a></li>
			<li><a href="#infoform">Course Info</a></li>
		</ul>
		<br class="clear">
	<?php endif; // dont show tabs ?>
	
	<?php
		messages($error, $message);
		if (!$stop) :
	?>
		<form action="" method="post" onsubmit="return saveRoute();">
			<fieldset>
				<div id="infoform" style="display:none">
					<label for="name">Name</label>
					<input name="name" id="name" type="text" value="<?php echo format($name); ?>" />
					<label for="city">City and/or State</label>
					<input name="city" id="city" type="text" value="<?php echo format($city); ?>" />
					<label for="distance">Distance</label>
					<input name="distance" id="distance" type="text" value="<?php echo format($distance); ?>" />
					<span id="units"><?php echo format($user->getUnits(true)); ?></span>
					
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
	
				<div id="gmapform">
					<div id="gmap-wrapper">
						<div id="overlay">

							<label for="location">Enter a location, then click at every interval of your route.</label>
							<input name="location" id="location" type="text" value="<?php echo format($location); ?>" />
							<br />
							
							<span id="status">
							<?php if (is_null($distance)) {
								echo '0.0';
							} else {
								echo format($distance);
							} ?> <?php echo format($user->getUnits(true)); ?></span>
							
							<span class="actions">
								<a onclick="startOver()">Start over</a> / <a onclick="stepBack()">Step back</a>
							</span>
							
						</div>
						<div id="gmap"></div>
					</div>
				</div>
	
				<input type="hidden" name="params" id="params" value="<?php echo $params; ?>" />
				<input name="action" type="submit" value="Save" class="button" />
			</fieldset>
		</form>
		
	<?php
		elseif (!is_null($prompt)) :
			echo $prompt;	
		endif; // no errors
	?>
</div>

<?php
require('footer.php');
?>