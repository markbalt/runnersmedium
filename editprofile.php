<?php
/*

Runner's Medium
http://www.runnersmedium.com/

editprofile.php

view and edit user profile info

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signinCheck(signin());

if ($user->getUnits() == 0) {
	$units = 'lbs';
} else {
	$units = 'kg';
}

$about = $location = $url = $why = $ispublic = $weight = $gender = null;
$birthDay   = 'Day';
$birthMonth = 'Month';
$birthYear  = 'Year';

// check for submit action
if (isset($_POST['action']) && $_POST['action'] == 'Save') {

	// get post data
	if (isset($_POST['about'])) {
		$about = $_POST['about'];
	}
	
	if (isset($_POST['location'])) {
		$location = $_POST['location'];
	}
	
	if (isset($_POST['url'])) {
		$url = $_POST['url'];
	}
	
	if (isset($_POST['why'])) {
		$why = $_POST['why'];
	}
	
	if (isset($_POST['ispublic'])) {
		$ispublic = $_POST['ispublic'];
	}
	
	if (isset($_POST['weight'])) {
		$weight = $_POST['weight'];
	}
	
	if (isset($_POST['birthmonth'])) {
		$birthMonth = $_POST['birthmonth'];
	}
	
	if (isset($_POST['birthday'])) {
		$birthDay = $_POST['birthday'];
	}
	
	if (isset($_POST['birthyear'])) {
		$birthYear = $_POST['birthyear'];
	}
	
	if (isset($_POST['gender'])) {
		$gender = $_POST['gender'];
	}
	
   	$sqlBday = null;
   	$gender  = (strcmp($gender, 'Select one') == 0) ? null : $gender;

    // validate
    if (strlen($about) > MAX_ABOUT) {
        $error = 'about you cannot be more than '.MAX_ABOUT.' characters';
    } elseif (strlen($location) > MAX_LOCATION) {
        $error = 'your location cannot be more than '.MAX_LOCATION.' characters';
    } elseif (strlen($url) > MAX_URL) {
        $error = 'URL cannot be more than '.MAX_URL.' characters';
    } elseif (strlen($why) > MAX_WHY) {
        $error = 'why I run cannot be more than '.MAX_WHY.' characters';
    } elseif ($ispublic != '0' && $ispublic != '1') {
        $error = 'invalid public profile response';
    } elseif (preg_match('/[^0-9]+$/', $weight) || $weight != null && ($weight > MAX_WEIGHT || $weight < 0)) {
        $error = 'invalid weight';
    } elseif (!is_null($gender) && !array_key_exists($gender, $user->getGenders())) {
    	$error = 'invalid gender';
    }
    
    // if either month day or year were changed, they must all be
    if (is_null($error)) {
    	if ((abs(strcmp($birthMonth, 'Month')) + abs(strcmp($birthDay, 'Day')) + abs(strcmp($birthYear, 'Year')) ) != 0) { // at least one of them was changed
			if (strcmp($birthMonth, 'Month') == 0 || (strcmp($birthDay, 'Day') == 0 || strcmp($birthYear, 'Year') == 0)) {

				// at least one of them was not changed
				$error = 'invalid birthday, please select all fields';

			} else {
			
				// validate birthday
				$strBday = "$birthYear-$birthMonth-$birthDay";

				// get today - 13 years (- 13 years only)
				$mustBe = (date('Y', time())-13)."-".date('n', time())."-".date('j', time());
				
				// check to make sure age is at least 13
				if (!checkdate($birthMonth, $birthDay, $birthYear)) {
					$error = 'invalid birthday, that day does not exist';
				} elseif (strtotime($strBday) > strtotime($mustBe)) {
					$error = 'invalid birthday, you must be at least 13 years old';
				}
			}
		}
    }	
    
    // update user info
    if (is_null($error)) {
    
    	// escape
    	$sqlabout    = mysql_real_escape_string($about);
		$sqllocation = mysql_real_escape_string($location);
		$sqlurl      = mysql_real_escape_string($url);
		$sqlwhy      = mysql_real_escape_string($why);
		$sqlispublic = mysql_real_escape_string($ispublic);
		$sqlweight   = (notempty($weight)) ? mysql_real_escape_string($weight) : 'NULL';
		$sqlgender   = mysql_real_escape_string($gender);
		
		
    	// convert weight to lbs before insert
    	if ($units == 'kg') {
    		$sqlweight *= 2.2;
    	}
		
		// format birthday for insert, allow null/reset
		if (isset($strBday)) {
			$sqlBday = ", birthday = '".mysql_real_escape_string($strBday)."'";
		} else {
			$sqlBday = ', birthday = NULL';
		}
		
		// update user
        $conn->query("UPDATE users SET about = '$sqlabout', location = '$sqllocation', url = '$sqlurl', why = '$sqlwhy', ispublic = $sqlispublic, weight = $sqlweight, gender = '$sqlgender' $sqlBday WHERE id = ".mysql_real_escape_string($user->ID())." LIMIT 1");
        $message = 'your profile has been saved.  <a href="'.$user->profile().'">Click here to see it.</a>';
    }
} else {
	// select current profile data
	$result = $conn->query("SELECT about, location, url, why, ispublic, weight, UNIX_TIMESTAMP(birthday), gender FROM users WHERE id = ".mysql_real_escape_string($user->ID())." LIMIT 1");
	
	if($conn->rowCount($result) == 0) {
        $error = 'User session error';
    } else {
		$line = $conn->fetchAssoc($result);
		
		$about    = $line['about'];
		$location = $line['location'];
		$url      = $line['url'];
		$why      = $line['why'];
		$ispublic = $line['ispublic'];
		$weight   = $line['weight'];
		$unixBday = $line['UNIX_TIMESTAMP(birthday)'];
		$gender   = $line['gender'];
		
		$gender = (!$gender) ? 'Select one' : $gender;
		
		// convert weight to kg before output
    	if ($units == 'kg') {
    		$weight /= 2.2;
    	}
		$weight = (!$weight) ? '' : $weight;
		
		// parse birthday
		if($unixBday)
		{
			$birthMonth = date('n', $unixBday);
			$birthDay = date('j', $unixBday);
			$birthYear = date('Y', $unixBday);
		}
	}
}

$title = 'Runner\'s Medium - Edit profile';
require('header.php');
?>

<div id="content">
	
	<ul id="tabnav">
		<li><a href="<?php echo root(); ?>settings/account">Account</a></li>
		<li><a class="select" href="<?php echo root(); ?>settings/editprofile">Edit Profile</a></li>
		<li><a href="<?php echo root(); ?>settings/picture">Picture</a></li>			
		<li><a href="<?php echo root(); ?>settings/password">Password</a></li>
		<li><a href="<?php echo root(); ?>settings/notices">Notices</a></li>
	</ul>
	<br class="clear" />
	
	<?php messages($error, $message); ?>

	<form action="" method="post" id="editprofileform">
		<fieldset>
			<label for="about">About me</label>
			<input name="about" id="about" type="text" value="<?php echo format($about); ?>" />
			
			<label for="location">Location</label>
			<input name="location" id="location" type="text" value="<?php echo format($location); ?>" />
			
			<label for="url">URL</label>
			<input name="url" id="url" type="text" value="<?php echo format($url); ?>" />
			
			<label for="why">Why I run</label>
			<input name="why" id="why" type="text" value="<?php echo format($why); ?>" />

			<label for="weight">Weight in <?php echo $units; ?> to calc calories</label>
			<input name="weight" id="weight" type="text" class="short" value="<?php echo format($weight); ?>" />
			
			<label for="birthmonth">Birthday</label>
								
			<select name="birthmonth" id="birthmonth">
				<?php echo arrayToSelect($user->getMonths(true), format($birthMonth)); ?>
			</select>
			<select name="birthday" id="birthday">
				<?php echo arrayToSelect($user->getDays(true), format($birthDay)); ?>
			</select>
			<select name="birthyear" id="birthyear">
				<?php echo arrayToSelect($user->getPastYears(true), format($birthYear)); ?>
			</select>
			
			<label for="gender">Gender</label>
			<select name="gender" id="gender">
				<?php echo arrayToSelect($user->getGenders(), format($gender)); ?>
			</select>

			<label for="public">Make my profile public?</label>
			<?php
				$publicCheck = ($ispublic) ? 'checked="checked"' : '';
				$privateCheck = ($ispublic) ? '' : 'checked="checked"';
			?>
			<fieldset class="radio">
				<input name="ispublic" id="public" type="radio" value="1" <?php echo $publicCheck; ?> /><label for="public">Public</label>
				<input name="ispublic" id="private" type="radio" value="0" <?php echo $privateCheck; ?> /><label for="private">Private</label>
			</fieldset>
			
			<input name="action" type="submit" value="Save" class="button" />
		</fieldset>
	</form>

</div>

<?php
require('footer.php');
?>